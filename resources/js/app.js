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

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('app-ready');
    initMobileMenu();
    initLocationSelects();
    initGallery();
    initSearchLoadingState();
    initFlashMessages();
});
