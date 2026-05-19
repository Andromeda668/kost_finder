import './bootstrap';
import locationList from 'indonesia-cities-regencies/list.json';

const initMobileMenu = () => {
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');

    if (!toggle || !menu) {
        return;
    }

    toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
};

const initLocationSelects = () => {
    const selects = document.querySelectorAll('[data-location-select]');

    if (!selects.length) {
        return;
    }

    const groupedLocations = locationList
        .map((location) => ({
            name: location.name,
            province: location.province,
        }))
        .sort((a, b) => {
            if (a.province === b.province) {
                return a.name.localeCompare(b.name, 'id');
            }

            return a.province.localeCompare(b.province, 'id');
        })
        .reduce((groups, location) => {
            if (!groups.has(location.province)) {
                groups.set(location.province, []);
            }

            groups.get(location.province).push(location.name);

            return groups;
        }, new Map());

    const provinceOptions = Array.from(groupedLocations.keys());

    const getProvinceByLocation = (locationName) => {
        if (!locationName) {
            return '';
        }

        for (const [province, locations] of groupedLocations) {
            if (locations.includes(locationName)) {
                return province;
            }
        }

        return '';
    };

    const populateLocationOptions = (select, province, selectedValue = '') => {
        const placeholder = select.dataset.placeholder || 'Pilih lokasi';

        select.innerHTML = '';
        select.append(new Option(placeholder, ''));

        if (!province || !groupedLocations.has(province)) {
            select.disabled = true;
            return;
        }

        groupedLocations.get(province).forEach((locationName) => {
            select.append(new Option(locationName, locationName));
        });

        select.disabled = false;

        if (selectedValue) {
            const hasSelectedOption = Array.from(select.options).some((option) => option.value === selectedValue);

            if (!hasSelectedOption) {
                select.append(new Option(selectedValue, selectedValue));
            }

            select.value = selectedValue;
        }
    };

    selects.forEach((select) => {
        const selectedValue = select.dataset.selected || '';
        const placeholder = select.dataset.placeholder || 'Pilih lokasi';
        const provinceSelect = select
            .closest('[data-location-group]')
            ?.querySelector('[data-location-province-select]');

        if (provinceSelect) {
            const selectedProvince = provinceSelect.dataset.selected || getProvinceByLocation(selectedValue);
            const provincePlaceholder = provinceSelect.dataset.placeholder || 'Pilih provinsi';

            provinceSelect.innerHTML = '';
            provinceSelect.append(new Option(provincePlaceholder, ''));

            provinceOptions.forEach((province) => {
                provinceSelect.append(new Option(province, province));
            });

            provinceSelect.value = selectedProvince;
            populateLocationOptions(select, selectedProvince, selectedValue);

            provinceSelect.addEventListener('change', () => {
                populateLocationOptions(select, provinceSelect.value);
            });

            return;
        }

        select.innerHTML = '';
        select.append(new Option(placeholder, ''));

        groupedLocations.forEach((locations, province) => {
            const optgroup = document.createElement('optgroup');
            optgroup.label = province;

            locations.forEach((locationName) => {
                const option = new Option(locationName, locationName);
                optgroup.append(option);
            });

            select.append(optgroup);
        });

        if (selectedValue) {
            const hasSelectedOption = Array.from(select.options).some((option) => option.value === selectedValue);

            if (!hasSelectedOption) {
                select.prepend(new Option(selectedValue, selectedValue));
            }

            select.value = selectedValue;
        }
    });
};

const initGallery = () => {
    const mainImage = document.querySelector('[data-gallery-main]');
    const thumbs = document.querySelectorAll('[data-gallery-thumb]');
    const prevButton = document.querySelector('[data-gallery-prev]');
    const nextButton = document.querySelector('[data-gallery-next]');

    if (!mainImage || !thumbs.length) {
        return;
    }

    let activeIndex = Array.from(thumbs).findIndex((thumb) => thumb.classList.contains('gallery-thumb-active'));
    activeIndex = activeIndex >= 0 ? activeIndex : 0;

    const setActiveImage = (index) => {
        activeIndex = (index + thumbs.length) % thumbs.length;
        const activeThumb = thumbs[activeIndex];

        mainImage.src = activeThumb.dataset.image || mainImage.src;
        thumbs.forEach((item) => item.classList.remove('gallery-thumb-active'));
        activeThumb.classList.add('gallery-thumb-active');
    };

    thumbs.forEach((thumb, index) => {
        thumb.addEventListener('click', () => setActiveImage(index));
    });

    prevButton?.addEventListener('click', () => setActiveImage(activeIndex - 1));
    nextButton?.addEventListener('click', () => setActiveImage(activeIndex + 1));
};

const initSearchLoadingState = () => {
    const form = document.querySelector('[data-search-form]');
    const loadingRegion = document.querySelector('[data-loading-region]') || document.querySelector('[data-search-results]');
    const resultsRegion = document.querySelector('[data-search-results]');

    if (!form) {
        return;
    }

    const query = new URLSearchParams(window.location.search);
    const shouldFocusResults = sessionStorage.getItem('kostFinderSearchSubmitted') === '1'
        || ['search', 'quick_location', 'max_price', 'sort'].some((key) => query.has(key) && query.get(key));

    if (resultsRegion && shouldFocusResults) {
        sessionStorage.removeItem('kostFinderSearchSubmitted');

        window.setTimeout(() => {
            resultsRegion.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }, 160);
    }

    form.addEventListener('submit', () => {
        sessionStorage.setItem('kostFinderSearchSubmitted', '1');
        form.classList.add('is-submitting');
        form.setAttribute('aria-busy', 'true');
        loadingRegion?.classList.add('is-loading');
    });
};

const initFlashMessages = () => {
    const messages = document.querySelectorAll('[data-flash-message]');

    messages.forEach((message) => {
        window.setTimeout(() => {
            message.classList.add('is-hiding');

            window.setTimeout(() => {
                message.remove();
            }, 300);
        }, 4500);
    });
};

const formatWithGrouping = (value, locale = 'id-ID') => {
    const digits = String(value || '').replace(/\D+/g, '');

    if (!digits) {
        return '';
    }

    const number = Number.parseInt(digits, 10);

    if (Number.isNaN(number)) {
        return '';
    }

    return new Intl.NumberFormat(locale).format(number);
};

const initMoneyInputs = () => {
    const currencySelect = document.querySelector('[data-currency-select]');
    const inputs = document.querySelectorAll('[data-money-input]');

    if (!inputs.length) {
        return;
    }

    const resolveLocale = () => (currencySelect?.value === 'IDR' ? 'id-ID' : 'en-US');

    inputs.forEach((input) => {
        input.addEventListener('input', () => {
            const locale = resolveLocale();
            const formatted = formatWithGrouping(input.value, locale);
            input.value = formatted;
        });
    });
};

const initNearbyRepeater = () => {
    const list = document.querySelector('[data-nearby-list]');
    const addButton = document.querySelector('[data-nearby-add]');

    if (!list || !addButton) {
        return;
    }

    const renderRow = (index) => {
        const card = document.createElement('div');
        card.className = 'nearby-card rounded-lg border border-[var(--line)] bg-[var(--surface-muted)] p-4';
        card.dataset.nearbyRow = '1';
        card.dataset.nearbyIndex = index;

        card.innerHTML = `
            <div class="grid gap-3 md:grid-cols-2">
                <div class="md:col-span-2 flex items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-[var(--ink)]">Tempat terdekat #${index + 1}</p>
                    <button type="button" class="table-link table-link-danger text-sm" data-nearby-remove>Hapus</button>
                </div>

                <label class="field-group">
                    <span>Nama Tempat</span>
                    <input type="text" name="nearby_places[${index}][label]" class="field-input" placeholder="Contoh: Universitas Indonesia" required>
                </label>

                <label class="field-group">
                    <span>Kategori</span>
                    <select name="nearby_places[${index}][category]" class="field-input">
                        <option value="universitas">Universitas</option>
                        <option value="kantor">Kantor</option>
                        <option value="transport">Transport</option>
                        <option value="lainnya" selected>Lainnya</option>
                    </select>
                </label>

                <label class="field-group md:col-span-2">
                    <span>Link Google Maps (opsional)</span>
                    <input type="url" name="nearby_places[${index}][google_maps_link]" class="field-input" placeholder="https://maps.app.goo.gl/..." data-nearby-maps-link>
                    <small class="text-xs text-[var(--muted)]">Buka Google Maps, cari tempat, salin link, dan tempel di sini.</small>
                </label>

                <label class="field-group">
                    <span>Jarak dari Kost (km)</span>
                    <input type="number" step="0.1" min="0.1" name="nearby_places[${index}][distance_km]" class="field-input" placeholder="Contoh: 1.5" required>
                    <small class="text-xs text-[var(--muted)]">Estimasi jarak dalam kilometer.</small>
                </label>
            </div>
        `;

        return card;
    };

    const currentRows = () => Array.from(list.querySelectorAll('[data-nearby-row]'));
    const nextIndex = () => currentRows().length;

    const removeEmptyState = () => {
        list.querySelector('.nearby-empty')?.remove();
    };

    addButton.addEventListener('click', () => {
        removeEmptyState();

        if (currentRows().length >= 8) {
            alert('Maksimal 8 tempat terdekat');
            return;
        }

        list.append(renderRow(nextIndex()));
    });

    list.addEventListener('click', (event) => {
        const button = event.target.closest('[data-nearby-remove]');
        if (!button) {
            return;
        }

        const row = button.closest('[data-nearby-row]');
        row?.remove();

        if (!currentRows().length) {
            const empty = document.createElement('div');
            empty.className = 'nearby-empty rounded-lg border border-[var(--line)] bg-[var(--surface-muted)] p-4 text-center text-sm text-[var(--muted)]';
            empty.textContent = 'Belum ada tempat terdekat ditambahkan. Klik tombol "Tambahkan" di atas untuk memulai.';
            list.append(empty);
        }
    });
};

const initFacilitiesRepeater = () => {
    const list = document.querySelector('[data-facility-list]');
    const addButton = document.querySelector('[data-facility-add]');

    if (!list || !addButton) {
        return;
    }

    const renderRow = () => {
        const row = document.createElement('div');
        row.className = 'nearby-row';
        row.dataset.facilityRow = '1';

        row.innerHTML = `
            <input type="text" name="fasilitas_items[]" class="field-input" placeholder="Contoh: WiFi" required>
            <div class="hidden sm:block"></div>
            <div class="hidden sm:block"></div>
            <button type="button" class="table-link table-link-danger" data-facility-remove>Hapus</button>
        `;

        return row;
    };

    addButton.addEventListener('click', () => {
        list.append(renderRow());
        list.querySelector('input[name="fasilitas_items[]"]:last-of-type')?.focus();
    });

    list.addEventListener('click', (event) => {
        const button = event.target.closest('[data-facility-remove]');
        if (!button) {
            return;
        }

        const row = button.closest('[data-facility-row], .nearby-row');
        row?.remove();
    });
};

const initUploadHint = () => {
    const input = document.querySelector('[data-upload-input]');
    const hint = document.querySelector('[data-upload-hint]');

    if (!input || !hint) {
        return;
    }

    const updateHint = () => {
        const count = input.files?.length || 0;
        hint.textContent = count ? `${count} file dipilih` : 'Belum ada file dipilih';
    };

    input.addEventListener('change', updateHint);
    updateHint();
};

const initMapsPreview = () => {
    const linkInput = document.querySelector('[data-maps-link]');
    const preview = document.querySelector('[data-maps-preview]');

    if (!linkInput || !preview) {
        return;
    }

    const update = () => {
        const url = linkInput.value?.trim();
        if (!url) {
            return;
        }

        try {
            const parsed = new URL(url);
            const q = parsed.searchParams.get('q') || parsed.searchParams.get('query') || '';
            if (q) {
                preview.src = `https://www.google.com/maps?q=${encodeURIComponent(q)}&output=embed`;
            }
        } catch {
            // ignore invalid URL while typing
        }
    };

    linkInput.addEventListener('change', update);
};

const initKostWizard = () => {
    const root = document.querySelector('[data-kost-wizard]');
    if (!root) {
        return;
    }

    const panels = Array.from(root.querySelectorAll('[data-wizard-step]'));
    const indicators = Array.from(root.querySelectorAll('[data-wizard-step-indicator]'));
    const prevButton = root.querySelector('[data-wizard-prev]');
    const nextButton = root.querySelector('[data-wizard-next]');
    const submitButton = root.querySelector('[data-wizard-submit]');
    const progress = root.querySelector('[data-wizard-progress]');

    if (!panels.length) {
        return;
    }

    const clamp = (value) => Math.min(Math.max(value, 1), panels.length);
    let step = 1;

    const showStep = (target) => {
        step = clamp(target);

        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.wizardStep !== String(step));
        });

        indicators.forEach((indicator) => {
            indicator.classList.toggle('wizard-step-active', indicator.dataset.wizardStepIndicator === String(step));
        });

        prevButton?.classList.toggle('hidden', step === 1);
        nextButton?.classList.toggle('hidden', step === panels.length);
        submitButton?.classList.toggle('hidden', step !== panels.length);

        if (progress) {
            progress.style.width = `${Math.round((step / panels.length) * 100)}%`;
        }

        panels.find((p) => p.dataset.wizardStep === String(step))?.querySelector('input,select,textarea')?.focus?.();
    };

    indicators.forEach((indicator) => {
        indicator.addEventListener('click', () => showStep(Number(indicator.dataset.wizardStepIndicator)));
    });

    prevButton?.addEventListener('click', () => showStep(step - 1));
    nextButton?.addEventListener('click', () => showStep(step + 1));

    showStep(1);
};

const initDigitsOnlyInputs = () => {
    const inputs = document.querySelectorAll('[data-digits-only]');

    inputs.forEach((input) => {
        input.addEventListener('beforeinput', (event) => {
            if (event.inputType === 'insertText' && /\D/.test(event.data || '')) {
                event.preventDefault();
            }
        });
    });
};

const initRentalTypeForm = () => {
    const options = document.querySelectorAll('[data-rental-option]');
    const durationInput = document.querySelector('[data-duration-input]');
    const hint = document.querySelector('[data-duration-hint]');

    if (!options.length || !durationInput) {
        return;
    }

    const applyType = (type) => {
        if (type === 'bulanan') {
            durationInput.max = '24';
            durationInput.placeholder = '1';
            hint && (hint.textContent = 'Bulanan maksimal 24 bulan.');
            return;
        }

        durationInput.max = '365';
        durationInput.placeholder = '1';
        hint && (hint.textContent = 'Harian maksimal 365 hari.');
    };

    const selected = Array.from(options).find((opt) => opt.checked)?.value || 'bulanan';
    applyType(selected);

    options.forEach((opt) => {
        opt.addEventListener('change', () => applyType(opt.value));
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('app-ready');
    initMobileMenu();
    initLocationSelects();
    initGallery();
    initSearchLoadingState();
    initFlashMessages();
    initMoneyInputs();
    initNearbyRepeater();
    initRentalTypeForm();
    initFacilitiesRepeater();
    initUploadHint();
    initMapsPreview();
    initKostWizard();
    initDigitsOnlyInputs();
});
