// Address Cascade System - ULTIMATE FIX VERSION
// Supports multiple instances with proper isolation

class AddressCascade {
    constructor(config) {
        this.provinceId = config.provinceId;
        this.regencyId = config.regencyId;
        this.districtId = config.districtId;
        this.villageId = config.villageId;
        this.baseUrl = config.baseUrl || '';
        
        // Unique identifier for this instance
        this.instanceId = `cascade_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        this.province = null;
        this.regency = null;
        this.district = null;
        this.village = null;
        
        // Store bound event handlers for cleanup
        this.handlers = {
            province: null,
            regency: null,
            district: null
        };
        
        // Queue untuk prevent multiple concurrent requests
        this.loadingQueue = {
            regencies: false,
            districts: false,
            villages: false
        };
        
        // Track if instance is active
        this.isActive = false;
        
        this.init();
    }
    
    // Initialize cascade
    init() {
        console.log(`🔧 [${this.instanceId}] Initializing address cascade...`, {
            provinceId: this.provinceId,
            regencyId: this.regencyId,
            districtId: this.districtId,
            villageId: this.villageId
        });
        
        this.province = document.getElementById(this.provinceId);
        this.regency = document.getElementById(this.regencyId);
        this.district = document.getElementById(this.districtId);
        this.village = document.getElementById(this.villageId);
        
        if (!this.checkElements()) {
            console.error(`❌ [${this.instanceId}] One or more dropdown elements not found`);
            return;
        }
        
        console.log(`✅ [${this.instanceId}] All dropdown elements found`);
        this.attachEventListeners();
        this.isActive = true;
    }
    
    // Check if all elements exist
    checkElements() {
        const elements = {
            province: this.province,
            regency: this.regency,
            district: this.district,
            village: this.village
        };
        
        let allFound = true;
        Object.keys(elements).forEach(key => {
            if (!elements[key]) {
                console.error(`❌ [${this.instanceId}] Element ${key} (${this[key + 'Id']}) not found!`);
                allFound = false;
            }
        });
        
        return allFound;
    }
    
    // Attach event listeners with proper binding
    attachEventListeners() {
        // Province change
        this.handlers.province = async (e) => {
            const provinceId = e.target.value;
            console.log(`📍 [${this.instanceId}] Province changed:`, provinceId);
            
            // Reset dependent dropdowns
            this.resetSelect(this.district, 'Pilih Kecamatan', true);
            this.resetSelect(this.village, 'Pilih Kelurahan/Desa', true);
            
            if (provinceId) {
                await this.loadRegencies(provinceId);
            } else {
                this.resetSelect(this.regency, 'Pilih Kabupaten/Kota', true);
            }
        };
        
        // Regency change
        this.handlers.regency = async (e) => {
            const regencyId = e.target.value;
            console.log(`🏙️ [${this.instanceId}] Regency changed:`, regencyId);
            
            // Reset dependent dropdowns
            this.resetSelect(this.village, 'Pilih Kelurahan/Desa', true);
            
            if (regencyId) {
                await this.loadDistricts(regencyId);
            } else {
                this.resetSelect(this.district, 'Pilih Kecamatan', true);
            }
        };
        
        // District change
        this.handlers.district = async (e) => {
            const districtId = e.target.value;
            console.log(`🗺️ [${this.instanceId}] District changed:`, districtId);
            
            if (districtId) {
                await this.loadVillages(districtId);
            } else {
                this.resetSelect(this.village, 'Pilih Kelurahan/Desa', true);
            }
        };
        
        // Remove old listeners first (if any)
        this.removeEventListeners();
        
        // Add new listeners
        this.province.addEventListener('change', this.handlers.province);
        this.regency.addEventListener('change', this.handlers.regency);
        this.district.addEventListener('change', this.handlers.district);
        
        console.log(`✅ [${this.instanceId}] Event listeners attached`);
    }
    
    // Remove event listeners
    removeEventListeners() {
        if (this.province && this.handlers.province) {
            this.province.removeEventListener('change', this.handlers.province);
        }
        if (this.regency && this.handlers.regency) {
            this.regency.removeEventListener('change', this.handlers.regency);
        }
        if (this.district && this.handlers.district) {
            this.district.removeEventListener('change', this.handlers.district);
        }
    }
    
    // Destroy instance and cleanup
    destroy() {
        console.log(`🗑️ [${this.instanceId}] Destroying cascade instance...`);
        
        this.removeEventListeners();
        
        // Mark as inactive
        this.isActive = false;
        
        // Clear references
        this.handlers = { province: null, regency: null, district: null };
        
        console.log(`✅ [${this.instanceId}] Cascade instance destroyed`);
    }
    
    // 🔥 FIX: Handle both direct array response and wrapped response
    async loadRegencies(provinceId) {
        if (this.loadingQueue.regencies) {
            console.log(`⏳ [${this.instanceId}] Regencies already loading, skipping...`);
            return;
        }
        
        this.loadingQueue.regencies = true;
        this.resetSelect(this.regency, 'Loading...', true);
        
        console.log(`📥 [${this.instanceId}] Fetching regencies for province:`, provinceId);
        
        try {
            const url = `${this.baseUrl}get-regencies/${provinceId}`;
            console.log(`🌐 [${this.instanceId}] Request URL:`, url);
            
            const response = await fetch(url);
            
            console.log(`📊 [${this.instanceId}] Response status:`, response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log(`✅ [${this.instanceId}] Raw response:`, data);
            
            // 🔥 Handle both formats:
            // Format 1: Direct array [{ id, name }, ...]
            // Format 2: Object with data property { data: [{ id, name }, ...] }
            let items = Array.isArray(data) ? data : (data.data || []);
            
            console.log(`✅ [${this.instanceId}] Regencies received:`, items.length, 'items');
            console.log(`📋 [${this.instanceId}] First item structure:`, items[0]);
            
            this.populateSelect(this.regency, items, 'Pilih Kabupaten/Kota');
            
        } catch (error) {
            console.error(`❌ [${this.instanceId}] Error fetching regencies:`, error);
            this.showError(this.regency, 'Error loading data');
        } finally {
            this.loadingQueue.regencies = false;
        }
    }
    
    // 🔥 FIX: Handle both direct array response and wrapped response
    async loadDistricts(regencyId) {
        if (this.loadingQueue.districts) {
            console.log(`⏳ [${this.instanceId}] Districts already loading, skipping...`);
            return;
        }
        
        this.loadingQueue.districts = true;
        this.resetSelect(this.district, 'Loading...', true);
        
        console.log(`📥 [${this.instanceId}] Fetching districts for regency:`, regencyId);
        
        try {
            const url = `${this.baseUrl}get-districts/${regencyId}`;
            console.log(`🌐 [${this.instanceId}] Request URL:`, url);
            
            const response = await fetch(url);
            
            console.log(`📊 [${this.instanceId}] Response status:`, response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log(`✅ [${this.instanceId}] Raw response:`, data);
            
            // 🔥 Handle both formats:
            let items = Array.isArray(data) ? data : (data.data || []);
            
            console.log(`✅ [${this.instanceId}] Districts received:`, items.length, 'items');
            console.log(`📋 [${this.instanceId}] First item structure:`, items[0]);
            
            this.populateSelect(this.district, items, 'Pilih Kecamatan');
            
        } catch (error) {
            console.error(`❌ [${this.instanceId}] Error fetching districts:`, error);
            this.showError(this.district, 'Error loading data');
        } finally {
            this.loadingQueue.districts = false;
        }
    }
    
    // 🔥 FIX: Handle both direct array response and wrapped response
    async loadVillages(districtId) {
        if (this.loadingQueue.villages) {
            console.log(`⏳ [${this.instanceId}] Villages already loading, skipping...`);
            return;
        }
        
        this.loadingQueue.villages = true;
        this.resetSelect(this.village, 'Loading...', true);
        
        console.log(`📥 [${this.instanceId}] Fetching villages for district:`, districtId);
        
        try {
            const url = `${this.baseUrl}get-villages/${districtId}`;
            console.log(`🌐 [${this.instanceId}] Request URL:`, url);
            
            const response = await fetch(url);
            
            console.log(`📊 [${this.instanceId}] Response status:`, response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log(`✅ [${this.instanceId}] Raw response:`, data);
            
            // 🔥 Handle both formats:
            let items = Array.isArray(data) ? data : (data.data || []);
            
            console.log(`✅ [${this.instanceId}] Villages received:`, items.length, 'items');
            console.log(`📋 [${this.instanceId}] First item structure:`, items[0]);
            
            this.populateSelect(this.village, items, 'Pilih Kelurahan/Desa');
            
        } catch (error) {
            console.error(`❌ [${this.instanceId}] Error fetching villages:`, error);
            this.showError(this.village, 'Error loading data');
        } finally {
            this.loadingQueue.villages = false;
        }
    }
    
    // Reset select element
    resetSelect(select, placeholder, disabled = false) {
        if (!select) return;
        select.innerHTML = `<option value="">-- ${placeholder} --</option>`;
        select.disabled = disabled;
    }
    
    // Populate select with data - 🔥 IMPROVED to handle edge cases
    populateSelect(select, data, placeholder) {
        if (!select) {
            console.warn(`❌ Select element is null, skipping populate`);
            return;
        }
        
        select.innerHTML = `<option value="">-- ${placeholder} --</option>`;
        
        if (data && Array.isArray(data) && data.length > 0) {
            // 🔥 ROBUST: Handle both camelCase (id, name) and different property names
            data.forEach(item => {
                const option = document.createElement('option');
                const itemId = item.id || item.Id || item.ID;
                const itemName = item.name || item.Name || item.NAME;
                
                if (!itemId || !itemName) {
                    console.warn(`⚠️ [${this.instanceId}] Item missing id or name:`, item);
                    return; // Skip this item
                }
                
                option.value = itemId;
                option.textContent = itemName;
                select.appendChild(option);
            });
            
            select.disabled = false;
            console.log(`✅ [${this.instanceId}] Populated ${data.length} items into select`);
        } else {
            select.innerHTML += '<option value="">-- Tidak ada data --</option>';
            select.disabled = true;
            console.log(`⚠️ [${this.instanceId}] No data to populate`);
        }
    }
    
    // Show error state
    showError(select, message) {
        if (!select) return;
        select.innerHTML = `<option value="">-- ${message} --</option>`;
        select.disabled = false;
    }
    
    // ✨ NEW: Load cascade with initial values (IMPROVED)
    async loadWithValues(provinceId, regencyId, districtId, villageId) {
        console.log(`🔄 [${this.instanceId}] Loading cascade with initial values:`, {
            provinceId, regencyId, districtId, villageId
        });
        
        try {
            // 1. Set province
            if (provinceId && provinceId !== 'null' && provinceId !== '') {
                this.province.value = provinceId;
                console.log(`✅ [${this.instanceId}] Province set:`, provinceId);
                
                // 2. Load regencies and wait
                await this.loadRegencies(provinceId);
                await this.waitForOptions(this.regency);
                
                // 3. Set regency if exists
                if (regencyId && regencyId !== 'null' && regencyId !== '') {
                    this.regency.value = regencyId;
                    console.log(`✅ [${this.instanceId}] Regency set:`, regencyId);
                    
                    // 4. Load districts and wait
                    await this.loadDistricts(regencyId);
                    await this.waitForOptions(this.district);
                    
                    // 5. Set district if exists
                    if (districtId && districtId !== 'null' && districtId !== '') {
                        this.district.value = districtId;
                        console.log(`✅ [${this.instanceId}] District set:`, districtId);
                        
                        // 6. Load villages and wait
                        await this.loadVillages(districtId);
                        await this.waitForOptions(this.village);
                        
                        // 7. Set village if exists
                        if (villageId && villageId !== 'null' && villageId !== '') {
                            this.village.value = villageId;
                            console.log(`✅ [${this.instanceId}] Village set:`, villageId);
                        }
                    }
                }
            }
            
            console.log(`✅ [${this.instanceId}] Cascade loaded successfully with all values`);
            return true;
            
        } catch (error) {
            console.error(`❌ [${this.instanceId}] Error loading cascade with values:`, error);
            return false;
        }
    }
    
    // ✨ NEW: Wait for select to have options
    waitForOptions(select, maxAttempts = 50) {
        return new Promise((resolve) => {
            let attempts = 0;
            
            const checkOptions = setInterval(() => {
                attempts++;
                
                // Check if has more than 1 option (default option + data)
                if (select.options.length > 1) {
                    clearInterval(checkOptions);
                    console.log(`✅ [${this.instanceId}] Options ready for ${select.id}`);
                    resolve(true);
                }
                
                // Timeout after maxAttempts
                if (attempts >= maxAttempts) {
                    clearInterval(checkOptions);
                    console.warn(`⚠️ [${this.instanceId}] Timeout waiting for options in ${select.id}`);
                    resolve(false);
                }
            }, 100);
        });
    }
    
    // Reset all dropdowns
    resetAll() {
        this.resetSelect(this.regency, 'Pilih Kabupaten/Kota', true);
        this.resetSelect(this.district, 'Pilih Kecamatan', true);
        this.resetSelect(this.village, 'Pilih Kelurahan/Desa', true);
        if (this.province) this.province.value = '';
    }
}

// Helper function untuk quick initialization
function initAddressCascade(provinceId, regencyId, districtId, villageId, baseUrl = '') {
    return new AddressCascade({
        provinceId: provinceId,
        regencyId: regencyId,
        districtId: districtId,
        villageId: villageId,
        baseUrl: baseUrl
    });
}