/**
 * ============================================
 * FormData Recursive Builder (KUBIX CORE)
 * ============================================
 * * Responsabilidad:
 * - Transformar objetos complejos de JavaScript en instancias de FormData.
 * - Soportar estructuras anidadas compatibles con el motor de PHP/Laravel (key[subKey]).
 * - Normalizar tipos de datos específicos (Booleanos a '1'/'0', Fechas a ISO).
 * - Manejar la carga de archivos (File/Blob) de forma transparente.
 * * Este puente es vital para peticiones que incluyen archivos (Multipart/Form-Data)
 * permitiendo que el desarrollador envíe objetos JSON simples desde la UI.
 */

/**
 * Constrói FormData de maneira recursiva e genérica.
 * * @param {Object} data - Objeto con los datos a serializar (Payload).
 * @param {string} [parentKey=null] - Referencia para recursión de llaves (uso interno).
 * @param {FormData} [formData=new FormData()] - Acumulador del estado del formulario.
 * @returns {FormData} - Instancia lista para ser enviada vía Axios.
 */
export function buildFormData(data, parentKey = null, formData = new FormData()) {
    
    /**
     * CASO BASE: Si el dato es un valor primitivo o nulo, se añade directamente.
     * Evitamos procesar 'undefined' para mantener la integridad de la API.
     */
    if (data === null || typeof data !== "object") {
        if (parentKey && data !== undefined) {
            // Los valores nulos se envían como cadena vacía para que Laravel los procese correctamente
            formData.append(parentKey, data === null ? "" : String(data));
        }
        return formData;
    }

    /**
     * PROCESAMIENTO RECURSIVO:
     * Iteración de propiedades bajo el principio de propiedad propia.
     */
    for (const key in data) {
        if (Object.prototype.hasOwnProperty.call(data, key)) {
            const value = data[key];
            
            // Ignoramos valores indefinidos para evitar el envío de strings "undefined"
            if (value === undefined) continue;

            /**
             * Generación de la llave siguiendo la convención de arrays de PHP.
             * Resultado esperado: 'user[profile][avatar]' o 'tags[0]'
             */
            const formKey = parentKey ? `${parentKey}[${key}]` : key;

            // --- 1. GESTIÓN DE BINARIOS (Archivos o Blobs) ---
            if (value instanceof File || value instanceof Blob) {
                formData.append(formKey, value, value.name || "blob");
            }
            
            // --- 2. RECURSIÓN PARA OBJETOS Y ARRAYS ---
            // Se excluyen Fechas y Nulls para evitar ciclos infinitos o errores de casting
            else if (
                typeof value === "object" &&
                value !== null &&
                !(value instanceof Date)
            ) {
                buildFormData(value, formKey, formData);
            }
            
            // --- 3. NORMALIZACIÓN DE BOOLEANOS (Sincronización con Laravel) ---
            // PHP interpreta el string "false" como true. Convertimos a 1/0 para ser precisos.
            else if (typeof value === "boolean") {
                formData.append(formKey, value ? "1" : "0");
            }
            
            // --- 4. FORMATEO DE FECHAS ---
            // Estandarización a ISO 8601 para facilitar el parseo en Carbon (PHP)
            else if (value instanceof Date) {
                formData.append(formKey, value.toISOString());
            }
            
            // --- 5. VALORES PRIMITIVOS ---
            // Manejo final de Strings, Numbers y Nulls
            else {
                formData.append(formKey, value === null ? "" : String(value));
            }
        }
    }

    return formData;
}