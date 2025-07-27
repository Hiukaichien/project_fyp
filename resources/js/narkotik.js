document.addEventListener('DOMContentLoaded', function() {
    // Function to handle enabling/disabling of "Lain-lain" text input
    function setupOtherInputToggle(radioName, otherInputId) {
        const radios = document.querySelectorAll(`input[name="${radioName}"]`);
        const otherInput = document.getElementById(otherInputId);

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'Lain-Lain') {
                    otherInput.disabled = false;
                    otherInput.focus();
                } else {
                    otherInput.disabled = true;
                    otherInput.value = ''; // Clear value when not 'Lain-lain'
                }
            });
        });

        // Initial state on page load
        const currentChecked = document.querySelector(`input[name="${radioName}"]:checked`);
        if (currentChecked && currentChecked.value === 'Lain-Lain') {
            otherInput.disabled = false;
        } else {
            otherInput.disabled = true;
        }
    }

    // Apply to Barang Kes "Status Pergerakan"
    setupOtherInputToggle('status_pergerakan_barang_kes', 'status_pergerakan_barang_kes_lain_narkotik');

    // Apply to Barang Kes "Status Selesai Siasatan"
    setupOtherInputToggle('status_barang_kes_selesai_siasatan', 'status_barang_kes_selesai_siasatan_lain_narkotik');

    // Apply to Barang Kes "Kaedah Pelupusan"
    setupOtherInputToggle('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', 'kaedah_pelupusan_barang_kes_lain_narkotik');

    // Function to handle "Ujian Makmal" text input for Status Pergerakan Barang Kes
    function setupUjianMakmalInput() {
        const radios = document.querySelectorAll('input[name="status_pergerakan_barang_kes"]');
        const makmalInput = document.getElementById('pergerakan_makmal_narkotik');

        function toggleMakmalInput() {
            const selectedValue = document.querySelector('input[name="status_pergerakan_barang_kes"]:checked')?.value;
            if (selectedValue === 'Ujian Makmal') {
                makmalInput.disabled = false;
            } else {
                makmalInput.disabled = true;
                makmalInput.value = '';
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', toggleMakmalInput);
        });

        // Initial state
        toggleMakmalInput();
    }

    // Initialize Ujian Makmal input logic
    setupUjianMakmalInput();

    // Function to handle "Dilupuskan ke Perbendaharaan" RM input for Status Barang Kes Selesai Siasatan
    function setupSelesaiSiasatanRMInput() {
        const radios = document.querySelectorAll('input[name="status_barang_kes_selesai_siasatan"]');
        const rmInput = document.getElementById('status_barang_kes_selesai_siasatan_RM_narkotik');

        function toggleRMInput() {
            const selectedValue = document.querySelector('input[name="status_barang_kes_selesai_siasatan"]:checked')?.value;
            if (selectedValue === 'Dilupuskan ke Perbendaharaan') {
                rmInput.disabled = false;
            } else {
                rmInput.disabled = true;
                rmInput.value = '';
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', toggleRMInput);
        });

        // Initial state
        toggleRMInput();
    }

    // Initialize Selesai Siasatan RM input logic
    setupSelesaiSiasatanRMInput();

    // Function to handle Jabatan Kimia keputusan field
    function setupJabatanKimiaKeputusan() {
        const dateInput = document.querySelector('input[name="tarikh_laporan_penuh_jabatan_kimia"]');
        const keputusanInput = document.getElementById('keputusan_laporan_jabatan_kimia');
        const statusRadios = document.querySelectorAll('input[name="status_laporan_penuh_jabatan_kimia"]');

        function checkAndToggleKeputusan() {
            const isReceived = document.querySelector('input[name="status_laporan_penuh_jabatan_kimia"]:checked')?.value === '1';
            const hasDate = dateInput && dateInput.value !== '';

            if (isReceived && hasDate) {
                keputusanInput.disabled = false;
            } else {
                keputusanInput.disabled = true;
                if (!isReceived) {
                    keputusanInput.value = '';
                }
            }
        }

        // Add event listeners
        if (dateInput) {
            dateInput.addEventListener('change', checkAndToggleKeputusan);
        }

        statusRadios.forEach(radio => {
            radio.addEventListener('change', checkAndToggleKeputusan);
        });

        // Initial check
        checkAndToggleKeputusan();
    }

    // Initialize Jabatan Kimia keputusan logic
    setupJabatanKimiaKeputusan();

    // Function to handle Jabatan Patalogi keputusan field
    function setupJabatanPatalogiKeputusan() {
        const dateInput = document.querySelector('input[name="tarikh_laporan_penuh_jabatan_patalogi"]');
        const keputusanInput = document.getElementById('keputusan_laporan_jabatan_patalogi');
        const statusRadios = document.querySelectorAll('input[name="status_laporan_penuh_jabatan_patalogi"]');

        function checkAndToggleKeputusan() {
            const isReceived = document.querySelector('input[name="status_laporan_penuh_jabatan_patalogi"]:checked')?.value === '1';
            const hasDate = dateInput && dateInput.value !== '';

            if (isReceived && hasDate) {
                keputusanInput.disabled = false;
            } else {
                keputusanInput.disabled = true;
                if (!isReceived) {
                    keputusanInput.value = '';
                }
            }
        }

        // Add event listeners
        if (dateInput) {
            dateInput.addEventListener('change', checkAndToggleKeputusan);
        }

        statusRadios.forEach(radio => {
            radio.addEventListener('change', checkAndToggleKeputusan);
        });

        // Initial check
        checkAndToggleKeputusan();
    }

    // Initialize Jabatan Patalogi keputusan logic
    setupJabatanPatalogiKeputusan();

    // Function to handle Forensik PDRM jenis barang kes field
    function setupForensikPDRMJenisBarangKes() {
        const dateInput = document.querySelector('input[name="tarikh_permohonan_laporan_forensik_pdrm"]');
        const jenisBarangKesInput = document.getElementById('jenis_barang_kes_di_hantar');
        const statusRadios = document.querySelectorAll('input[name="status_permohonan_laporan_forensik_pdrm"]');

        function checkAndToggleJenisBarangKes() {
            const isRequested = document.querySelector('input[name="status_permohonan_laporan_forensik_pdrm"]:checked')?.value === '1';
            const hasDate = dateInput && dateInput.value !== '';

            if (isRequested && hasDate) {
                jenisBarangKesInput.disabled = false;
            } else {
                jenisBarangKesInput.disabled = true;
                if (!isRequested) {
                    jenisBarangKesInput.value = '';
                }
            }
        }

        // Add event listeners
        if (dateInput) {
            dateInput.addEventListener('change', checkAndToggleJenisBarangKes);
        }

        statusRadios.forEach(radio => {
            radio.addEventListener('change', checkAndToggleJenisBarangKes);
        });

        // Initial check
        checkAndToggleJenisBarangKes();
    }

    // Initialize Forensik PDRM jenis barang kes logic
    setupForensikPDRMJenisBarangKes();
});
