/**
 * MÓDULO DE DATOS Y GEOJSON DINÁMICO - VERSIÓN SUPERADMIN PRO
 * - Incluye motor de GeoJSON (IBGE + Natural Earth + Nominatim)
 * - Incluye motor de KPIs con historial para ECharts Sparklines
 */

/* ================= BASE DE DATOS FAKE ================= */
export const DB = {
  countries: {
    BR: {
      name: "Brasil", iso3: "BRA", users: 620000, revenue: 1800000, mrr: 150000, lat: -14.235, lng: -51.9253,
      states: {
        PR: {
          name: "Paraná", osmId: "161537", users: 180000, revenue: 520000, mrr: 43000, lat: -24.89, lng: -51.55,
          cities: {
            Curitiba: {
              osmId: "5471878", users: 95000, revenue: 280000, mrr: 24000, lat: -25.4278, lng: -49.2733,
              neighborhoods: {
                Centro: { 
                  users: 25000, revenue: 82000, mrr: 6800, lat: -25.43, lng: -49.27,
                  businesses: Array.from({ length: 15 }, (_, i) => ({
                    id: `br-pr-cur-cen-${i}`, label: `Negocio Centro ${i + 1}`, 
                    lat: -25.43 + (Math.random() * 0.01), lng: -49.27 + (Math.random() * 0.01), 
                    revenue: Math.floor(Math.random() * 5000) + 1000
                  }))
                },
                Pinheirinho: { 
                  users: 42000, revenue: 100000, mrr: 9000, lat: -25.51, lng: -49.29,
                  businesses: Array.from({ length: 10 }, (_, i) => ({
                    id: `br-pr-cur-pin-${i}`, label: `Socio Pinheirinho ${i + 1}`, 
                    lat: -25.51 + (Math.random() * 0.01), lng: -49.29 + (Math.random() * 0.01), 
                    revenue: Math.floor(Math.random() * 3000) + 500
                  }))
                },
              },
            },
          },
        },
        SP: {
          name: "São Paulo", osmId: "298204", users: 260000, revenue: 820000, mrr: 68000, lat: -23.55, lng: -46.64,
          cities: {
            "São Paulo": {
              osmId: "298285", users: 180000, revenue: 580000, mrr: 48000, lat: -23.55, lng: -46.64,
              neighborhoods: {
                Pinheiros: { 
                    users: 70000, revenue: 220000, mrr: 18000, lat: -23.56, lng: -46.69,
                    businesses: Array.from({ length: 20 }, (_, i) => ({
                        id: `br-sp-sp-pin-${i}`, label: `Tech Pinheiros ${i + 1}`, 
                        lat: -23.56 + (Math.random() * 0.02), lng: -46.69 + (Math.random() * 0.02), 
                        revenue: Math.floor(Math.random() * 12000) + 3000
                    }))
                },
              },
            },
          },
        },
      },
    },
    MX: {
      name: "México", iso3: "MEX", users: 410000, revenue: 1200000, mrr: 98000, lat: 23.6345, lng: -102.5528,
      states: {
        CMX: {
          name: "CDMX", osmId: "1376330", users: 210000, revenue: 620000, mrr: 52000, lat: 19.4326, lng: -99.1332,
          cities: {
            "Ciudad de México": {
              osmId: "1376330", users: 210000, revenue: 620000, mrr: 52000, lat: 19.4326, lng: -99.1332,
              neighborhoods: {
                Polanco: { 
                    users: 72000, revenue: 210000, mrr: 18000, lat: 19.43, lng: -99.20,
                    businesses: Array.from({ length: 25 }, (_, i) => ({
                        id: `mx-cmx-cdmx-pol-${i}`, label: `Polanco Luxury ${i + 1}`, 
                        lat: 19.43 + (Math.random() * 0.01), lng: -99.20 + (Math.random() * 0.01), 
                        revenue: Math.floor(Math.random() * 15000) + 5000
                    }))
                },
              },
            },
          },
        },
      },
    },
    CO: { name: "Colombia", iso3: "COL", users: 260000, revenue: 740000, mrr: 61000, lat: 4.5709, lng: -74.2973, states: {} },
    AR: { name: "Argentina", iso3: "ARG", users: 230000, revenue: 690000, mrr: 57000, lat: -38.4161, lng: -63.6167, states: {} },
  },
};

/* ================= UTILS ================= */
const GEO_CACHE = new Map();
function sum(arr) { return arr.reduce((a, b) => a + b, 0); }

/** Genera puntos de historial aleatorios para simular una curva de crecimiento */
function generateHistory(currentValue, points = 7) {
    const history = [];
    let temp = currentValue;
    for (let i = 0; i < points; i++) {
        const variance = 0.85 + Math.random() * 0.3;
        history.unshift(Math.floor(temp));
        temp = temp * variance;
    }
    return history;
}

/* ================= MOTOR GEOJSON ================= */
export async function loadGeoJSON({ level, country, state, city, neighborhood }) {
  const cacheKey = `${level}-${country}-${state}-${city}-${neighborhood}`;
  if (GEO_CACHE.has(cacheKey)) return GEO_CACHE.get(cacheKey);

  let geojsonData = null;
  try {
    switch (level) {
      case "Global": geojsonData = await fetchGlobalFiltered(); break;
      case "Country": geojsonData = await fetchCountryGeoJSON(country); break;
      case "State": geojsonData = await fetchStateGeoJSON(country, state); break;
      case "City": geojsonData = await fetchCityGeoJSON(country, state, city); break;
      case "Neighborhood": geojsonData = await fetchNominatim(neighborhood, city); break;
      default: return [];
    }
    if (geojsonData) GEO_CACHE.set(cacheKey, geojsonData);
    return geojsonData;
  } catch (error) {
    console.error("❌ Error GeoJSON Engine:", error);
    return [];
  }
}

async function fetchGlobalFiltered() {
  const url = "https://raw.githubusercontent.com/nvkelso/natural-earth-vector/master/geojson/ne_110m_admin_0_countries.geojson";
  const res = await fetch(url);
  const data = await res.json();
  const activeISOs = Object.values(DB.countries).map(c => c.iso3);
  const filteredFeatures = data.features.filter(f => 
    activeISOs.includes(f.properties.ADM0_A3) || activeISOs.includes(f.properties.ISO_A3)
  );
  return [{ type: "FeatureCollection", features: filteredFeatures }];
}

async function fetchCountryGeoJSON(countryCode) {
  const url = `https://raw.githubusercontent.com/datasets/geo-countries/master/data/${countryCode.toLowerCase()}.geojson`;
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error();
    return [await res.json()];
  } catch {
    const global = await fetchGlobalFiltered();
    const feature = global[0].features.find(f => f.properties.ISO_A2 === countryCode);
    return feature ? [{ type: "FeatureCollection", features: [feature] }] : [];
  }
}

async function fetchStateGeoJSON(countryCode, stateCode) {
  if (countryCode === "BR") {
    const ibgeCodes = { "PR": "41", "SP": "35", "RJ": "33" };
    const code = ibgeCodes[stateCode];
    if (code) {
      const res = await fetch(`https://servicodados.ibge.gov.br/api/v3/malhas/estados/${code}?formato=application/vnd.geo+json`);
      return [await res.json()];
    }
  }
  return await fetchNominatim(DB.countries[countryCode].states[stateCode]?.name || "", DB.countries[countryCode]?.name || "");
}

async function fetchCityGeoJSON(countryCode, stateCode, cityName) {
  return await fetchNominatim(cityName, DB.countries[countryCode].states[stateCode]?.name || "");
}

async function fetchNominatim(query, context) {
  if (!query) return [];
  const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(`${query}, ${context}`)}&format=geojson&polygon_geojson=1&limit=1`;
  const res = await fetch(url, { headers: { "User-Agent": "DashboardApp/1.0" } });
  const data = await res.json();
  return data.features?.length ? [data] : [];
}

/* ================= MOTOR DE DATOS (SUPERADMIN) ================= */
export function getDashboardData({ level, country, state, city, neighborhood }) {
  if (level === "Global") return buildGlobal();
  if (level === "Country") return buildCountry(country);
  if (level === "State") return buildState(country, state);
  if (level === "City") return buildCity(country, state, city);
  if (level === "Neighborhood") return buildNeighborhood(country, state, city, neighborhood);
  return buildGlobal();
}

function buildGlobal() {
  const countries = Object.entries(DB.countries);
  const totalUsers = sum(countries.map(([, c]) => c.users || 0));
  const totalRev = sum(countries.map(([, c]) => c.revenue || 0));
  return {
    kpis: [
      { id: 'g1', title: "Total Revenue", value: `$${(totalRev / 1000000).toFixed(2)}M`, trend: 3, history: generateHistory(totalRev) },
      { id: 'g2', title: "Total Users", value: totalUsers.toLocaleString(), trend: 12, history: generateHistory(totalUsers) },
      { id: 'g3', title: "Markets Active", value: countries.length, trend: 0, history: [2, 3, 4, 4, 5] },
      { id: 'g4', title: "Efficiency Index", value: `$${(totalRev / (totalUsers || 1)).toFixed(2)}/u`, trend: 5, history: [2.1, 2.3, 2.5, 2.8, 2.9] }
    ],
    mapPoints: countries.map(([code, c]) => ({ id: code, label: c.name, lat: c.lat, lng: c.lng, type: 'area' })),
    distribution: countries.map(([code, c]) => ({ name: c.name, sub: "País", value: c.revenue || 0 })),
  };
}

function buildCountry(code) {
  const c = DB.countries[code] || { states: {}, users: 0, revenue: 0, mrr: 0 };
  const states = Object.entries(c.states);
  return {
    kpis: [
        { id: 'c1', title: "Country Revenue", value: `$${(c.revenue / 1000).toFixed(1)}K`, trend: 8, history: generateHistory(c.revenue) },
        { id: 'c2', title: "Country Users", value: (c.users || 0).toLocaleString(), trend: 11, history: generateHistory(c.users) },
        { id: 'c3', title: "Avg MRR", value: `$${(c.mrr || 0).toLocaleString()}`, trend: 4, history: generateHistory(c.mrr) },
        { id: 'c4', title: "Status", value: "Active", trend: 0, history: [1, 1, 1, 1, 1] }
    ],
    mapPoints: states.map(([code, s]) => ({ id: code, label: s.name, lat: s.lat, lng: s.lng, type: 'area' })),
    distribution: states.map(([code, s]) => ({ name: s.name, sub: "Estado", value: s.revenue || 0 })),
  };
}

function buildState(countryCode, stateCode) {
  const s = DB.countries[countryCode]?.states[stateCode] || { cities: {}, users: 0, revenue: 0, mrr: 0 };
  const cities = Object.entries(s.cities);
  return {
    kpis: [
        { id: 's1', title: "State Revenue", value: `$${(s.revenue || 0).toLocaleString()}`, trend: 5, history: generateHistory(s.revenue) },
        { id: 's2', title: "State Users", value: (s.users || 0).toLocaleString(), trend: 12, history: generateHistory(s.users) },
        { id: 's3', title: "Active Cities", value: cities.length, trend: 2, history: [1, 1, 2, 2, 2] },
        { id: 's4', title: "State MRR", value: `$${(s.mrr || 0).toLocaleString()}`, trend: 6, history: generateHistory(s.mrr) }
    ],
    mapPoints: cities.map(([name, c]) => ({ id: name, label: name, lat: c.lat, lng: c.lng, type: 'area' })),
    distribution: cities.map(([name, c]) => ({ name, sub: "Ciudad", value: c.revenue || 0 })),
  };
}

function buildCity(countryCode, stateCode, cityName) {
  const city = DB.countries[countryCode]?.states[stateCode]?.cities[cityName] || { neighborhoods: {}, users: 0, revenue: 0, mrr: 0 };
  const neighborhoods = Object.entries(city.neighborhoods);
  return {
    kpis: [
        { id: 'ci1', title: "City Revenue", value: `$${(city.revenue || 0).toLocaleString()}`, trend: 9, history: generateHistory(city.revenue) },
        { id: 'ci2', title: "City Users", value: (city.users || 0).toLocaleString(), trend: 14, history: generateHistory(city.users) },
        { id: 'ci3', title: "City MRR", value: `$${(city.mrr || 0).toLocaleString()}`, trend: 10, history: generateHistory(city.mrr) },
        { id: 'ci4', title: "Coverage", value: "92%", trend: 2, history: [85, 88, 92] }
    ],
    mapPoints: neighborhoods.map(([name, n]) => ({ id: name, label: name, lat: n.lat, lng: n.lng, type: 'area' })),
    distribution: neighborhoods.map(([name, n]) => ({ name, sub: "Barrio", value: n.revenue || 0 })),
  };
}

function buildNeighborhood(countryCode, stateCode, cityName, neighborhoodName) {
  const n = DB.countries[countryCode]?.states[stateCode]?.cities[cityName]?.neighborhoods[neighborhoodName] || { businesses: [], users: 0, revenue: 0, mrr: 0 };
  const businesses = n.businesses || [];
  return {
    kpis: [
        { id: 'n1', title: "Area Revenue", value: `$${(n.revenue || 0).toLocaleString()}`, trend: 2, history: generateHistory(n.revenue) },
        { id: 'n2', title: "Area Users", value: (n.users || 0).toLocaleString(), trend: 5, history: generateHistory(n.users) },
        { id: 'n3', title: "Active Partners", value: businesses.length, trend: 0, history: generateHistory(businesses.length) },
        { id: 'n4', title: "Density Index", value: (businesses.length / 1.5).toFixed(1), trend: 0, history: [2, 4, 5, 6] }
    ],
    mapPoints: businesses.map(b => ({ ...b, type: 'business' })),
    distribution: businesses.map(b => ({ name: b.label, sub: "Contrato", value: b.revenue || 0 })),
  };
}

export function getAvailableFilters(countryCode, stateCode = null) {
  const country = DB.countries[countryCode];
  if (!country) return { states: [], cities: [], neighborhoods: () => [] };
  return {
    states: Object.entries(country.states || {}).map(([code, s]) => ({ code, name: s.name })),
    cities: stateCode ? Object.keys(country.states[stateCode]?.cities || {}) : [],
    neighborhoods: (city) => Object.keys(country.states[stateCode]?.cities?.[city]?.neighborhoods || {}),
  };
}