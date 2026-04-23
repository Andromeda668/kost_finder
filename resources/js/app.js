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

    selects.forEach((select) => {
        const selectedValue = select.dataset.selected || '';
        const placeholder = select.dataset.placeholder || 'Pilih lokasi';

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
            select.value = selectedValue;
        }
    });
};

const initGallery = () => {
    const mainImage = document.querySelector('[data-gallery-main]');
    const thumbs = document.querySelectorAll('[data-gallery-thumb]');

    if (!mainImage || !thumbs.length) {
        return;
    }

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            mainImage.src = thumb.dataset.image || mainImage.src;

            thumbs.forEach((item) => item.classList.remove('gallery-thumb-active'));
            thumb.classList.add('gallery-thumb-active');
        });
    });
};

const initSearchLoadingState = () => {
    const form = document.querySelector('[data-search-form]');
    const loadingRegion = document.querySelector('[data-loading-region]');

    if (!form || !loadingRegion) {
        return;
    }

    form.addEventListener('submit', () => {
        loadingRegion.classList.add('is-loading');
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('app-ready');
    initMobileMenu();
    initLocationSelects();
    initGallery();
    initSearchLoadingState();
});
