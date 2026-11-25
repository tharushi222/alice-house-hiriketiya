/**
 * Alice House Hiriketiya - Client Scripts
 * Dynamic Calculations, Validations, and UI Interactivity
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Menu Toggle
    const mobileBtn = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }

    // 2. Date Input Constraints (Check-in must be >= today, check-out > check-in)
    const checkinInput = document.getElementById('check_in');
    const checkoutInput = document.getElementById('check_out');

    if (checkinInput && checkoutInput) {
        // Set minimum check-in to today
        const today = new Date().toISOString().split('T')[0];
        if (!checkinInput.getAttribute('min')) {
            checkinInput.setAttribute('min', today);
        }

        function updateCheckoutMin() {
            if (checkinInput.value) {
                const checkinDate = new Date(checkinInput.value);
                const nextDay = new Date(checkinDate);
                nextDay.setDate(checkinDate.getDate() + 1);
                const minCheckout = nextDay.toISOString().split('T')[0];
                checkoutInput.setAttribute('min', minCheckout);

                if (checkoutInput.value && checkoutInput.value <= checkinInput.value) {
                    checkoutInput.value = minCheckout;
                }
            }
        }

        checkinInput.addEventListener('change', function () {
            updateCheckoutMin();
            calculateLiveCost();
        });

        checkoutInput.addEventListener('change', function () {
            calculateLiveCost();
        });
    }

    // 3. Dynamic Cost Calculator for Booking Widgets
    function calculateLiveCost() {
        const checkinEl = document.getElementById('check_in');
        const checkoutEl = document.getElementById('check_out');
        const pricePerNightEl = document.getElementById('price_per_night');
        const totalNightsEl = document.getElementById('calc_nights');
        const totalPriceEl = document.getElementById('calc_total');
        const totalLkrEl = document.getElementById('calc_total_lkr');
        const hiddenTotalInput = document.getElementById('total_price_input');

        if (!checkinEl || !checkoutEl || !pricePerNightEl || !totalNightsEl || !totalPriceEl) {
            return;
        }

        const checkin = new Date(checkinEl.value);
        const checkout = new Date(checkoutEl.value);
        const pricePerNight = parseFloat(pricePerNightEl.value || pricePerNightEl.dataset.price || 0);

        if (!isNaN(checkin.getTime()) && !isNaN(checkout.getTime()) && checkout > checkin) {
            const diffTime = Math.abs(checkout - checkin);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const nights = Math.max(1, diffDays);
            const total = (nights * pricePerNight).toFixed(2);
            const totalLkr = Math.round(total * 310).toLocaleString();

            totalNightsEl.textContent = nights + ' night' + (nights > 1 ? 's' : '');
            totalPriceEl.textContent = '$' + total;
            if (totalLkrEl) {
                totalLkrEl.textContent = '≈ LKR ' + totalLkr;
            }
            if (hiddenTotalInput) {
                hiddenTotalInput.value = total;
            }
        } else {
            totalNightsEl.textContent = '1 night';
            totalPriceEl.textContent = '$' + pricePerNight.toFixed(2);
            if (totalLkrEl) {
                totalLkrEl.textContent = '≈ LKR ' + Math.round(pricePerNight * 310).toLocaleString();
            }
            if (hiddenTotalInput) {
                hiddenTotalInput.value = pricePerNight.toFixed(2);
            }
        }
    }

    // Trigger initial calculation if elements exist
    calculateLiveCost();

    // 4. Room Image Gallery Thumbnail Click to Switch Main Image
    const galleryThumbs = document.querySelectorAll('.gallery-thumb-item');
    const mainGalleryImg = document.getElementById('mainGalleryImg');

    if (galleryThumbs.length > 0 && mainGalleryImg) {
        galleryThumbs.forEach(thumb => {
            thumb.addEventListener('click', function () {
                const newSrc = this.getAttribute('data-full') || this.src;
                mainGalleryImg.src = newSrc;
                galleryThumbs.forEach(t => t.style.opacity = '0.7');
                this.style.opacity = '1';
            });
        });
    }

    // 5. Auto dismiss flash alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 6000);
    });
});
