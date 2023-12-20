;(function () {
    'use strict';

    customElements.define('release-filters', class extends HTMLElement {

        /**
         * The class constructor object
         */
        constructor () {

            // Always call super first in constructor
            super();

            // Set up variables.
            this.date = this.querySelector('[data-type="date"]');
            this.publisher = this.querySelector('[data-type="publisher"]');
            this.filter = this.querySelector('[data-btn="filter"]');
            this.clear = this.querySelector('[data-btn="clear"]');
            this.sections = document.querySelectorAll('.manga-release');
            this.cards = document.querySelectorAll('.manga-release-card');
            this.warning = document.querySelector('.manga-release-warning');

            // Add event listeners.
            this.filter.addEventListener('click', this);
            this.clear.addEventListener('click', this);

            // Add toast wrapper.
            const toast = document.createElement('div');
            toast.classList.add('manga-release-toast');
            document.body.append(toast);

            // Add view-transition-name to each section.
            for ( let [index, section] of this.sections.entries() ) {
                section.style.viewTransitionName = `section-${index + 1}`;
            }

            // Add view-transition-name to each card.
            for ( let [index, card] of this.cards.entries() ) {
                card.style.viewTransitionName = `card-${index + 1}`;
            }
        }

        // Handle events
        handleEvent (event) {

            if ( !document.startViewTransition ) {
                
                // If user clicked on filter button.
                if ( event.target === this.filter ) {
                    this.cardRevealHandler();
                }

                // If user clicked on clear button.
                if ( event.target === this.clear ) {
                    this.date.value = "";
                    this.publisher.value = "";
                    this.toastHandler('Filters cleared');
                    this.clearHandler();
                }
                return;

            }

            document.startViewTransition(() => {

                // If user clicked on filter button.
                if ( event.target === this.filter ) {
                    this.cardRevealHandler();
                }

                // If user clicked on clear button.
                if ( event.target === this.clear ) {
                    this.date.value = "";
                    this.publisher.value = "";
                    this.toastHandler('Filters cleared');
                    this.clearHandler();
                }

            });
        }

        // Handle card reveal events.
        cardRevealHandler () {

            // Hide all sections by default.
            for ( let section of this.sections ) {
                section.classList.add('manga-release--hidden');
            }

            // Cycle through each card.
            for ( let card of this.cards ) {

                // Hide all cards by default.
                card.classList.add('manga-release-card--hidden');

                // Get parent section date.
                let parent = card.closest('.manga-release');


                // If both date and publisher are selected.
                if ( !!this.date.value && !!this.publisher.value ) {

                    // If publisher doesn't match, bail.
                    if ( this.publisher.value !== card.getAttribute('data-publisher') ) continue;

                    // If parent section date doesn't match, bail.
                    if ( parent.getAttribute('data-date') !== this.date.value ) continue;

                    // Unhide individual card.
                    card.classList.remove('manga-release-card--hidden');

                    // Unhide parent section.
                    parent.classList.remove('manga-release--hidden');

                // If only date is selected.
                } else if ( !!this.date.value ) {

                    // If parent section date doesn't match, bail.
                    if ( parent.getAttribute('data-date') !== this.date.value ) continue;

                    // Unhide parent section.
                    parent.classList.remove('manga-release--hidden');

                    // Unhide individual card.
                    card.classList.remove('manga-release-card--hidden');

                // If only publisher is selected.
                } else if ( !!this.publisher.value ) {

                    // If publisher doesn't match, bail.
                    if ( this.publisher.value !== card.getAttribute('data-publisher') ) continue;

                    // Unhide parent section.
                    parent.classList.remove('manga-release--hidden');

                    // Unhide individual card.
                    card.classList.remove('manga-release-card--hidden');

                // Nothing was selected.
                } else {
                    this.clearHandler();
                }
            }

            // Cycle back through each section.
            for ( let section of this.sections ) {

                if ( !section.classList.contains('manga-release--hidden') ) {

                    // At least 1 section is shown, so hide warning section.
                    this.warning.setAttribute('hidden', '');
                    break;
                }

                // No section is shown, reveal warning section.
                this.warning.removeAttribute('hidden');
            }

            let toastText = 'There are no items to show';
            let cardsShown = document.querySelectorAll('.manga-release-card:not(.manga-release-card--hidden)');
            let cardCount = cardsShown.length;
            if ( cardCount === 1 ) {
                toastText = `Showing ${cardCount} item`;
            } else if ( cardCount > 1 ) {
                toastText = `Showing ${cardCount} items`;
            }
            this.toastHandler(toastText);
        }

        // Handle toast events.
        toastHandler( text ) {
            let output = document.createElement('output');
            output.classList.add('manga-release-toast__output');
            output.setAttribute('role', 'status');
            output.setAttribute('aria-live', 'polite');
            output.innerText = text;

            let toastWrapper = document.querySelector('.manga-release-toast');
            if ( toastWrapper ) {
                toastWrapper.append(output);
            }
            setTimeout( () => {
                output.remove();
            }, 3150 );
        }

        // Handle clear events.
        clearHandler () {

            // Unhide all sections.
            for( let section of this.sections ) {
                section.classList.remove('manga-release--hidden');
            }

            // Unhide all cards.
            for( let card of this.cards ) {
                card.classList.remove('manga-release-card--hidden');
            }

            // Hide warning section.
            this.warning.setAttribute('hidden', '');
        }
    });
})();