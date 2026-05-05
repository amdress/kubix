import { reactive } from "vue";

const initialState = {
    branchData: {},
    addressData: {},
    solutions: [],
    hasManager: false, 
    manager: {},
    managerAddress: {},
    hasManagerAddress: false,
};

// Instancia única compartida
const state = reactive(JSON.parse(JSON.stringify(initialState)));

export function useBranchWizard() {
    
    const setBranchData = (data) => {
        console.log("📥 [STORE]: Branch Data", data);
        state.branchData = data;
    };

    const setAddressData = (data) => {
        const { has_manager, ...address } = data;
        console.log("📥 [STORE]: Branch Address", address);
        state.addressData = address;

        if (has_manager !== undefined) {
            console.log("🚦 [STORE]: Has Manager?", has_manager);
            state.hasManager = has_manager;
        }
    };

    const setManagerData = (data) => {
        console.log("📥 [STORE]: Manager Personal Data", data);
        state.manager = data;
    };

    const setManagerAddress = (data) => {
        console.log("📥 [STORE]: Manager Address", data);
        state.managerAddress = data;
    };

    const setSolutions = (solutionIds) => {
        console.log("📥 [STORE]: Solutions IDs", solutionIds);
        state.solutions = solutionIds;
    };

    const reset = () => {
        Object.assign(state, JSON.parse(JSON.stringify(initialState)));
    };

    return { 
        state, 
        setBranchData, 
        setAddressData, 
        setManagerData,
        setManagerAddress,
        setSolutions,
        reset 
    };
}