/**
 * LUX ÎLES - Header Loader
 * Charge le header de manière réutilisable sur toutes les pages
 */

/**
 * Charge le header dans un élément avec l'id "header-container" ou remplace un header existant
 */
function loadHeader() {
    const headerPath = 'includes/header.html';
    const headerContainer = document.getElementById('header-container');
    const existingHeader = document.querySelector('header.lux-header');
    
    fetch(headerPath)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            // Si un container existe, l'utiliser
            if (headerContainer) {
                headerContainer.innerHTML = html;
                setTimeout(() => {
                    updateActiveNavLinks();
                    // Appeler aussi initNavActiveLinks de main.js si elle existe
                    if (window.initNavActiveLinks) {
                        window.initNavActiveLinks();
                    }
                }, 50);
            }
            // Sinon, remplacer le header existant
            else if (existingHeader) {
                existingHeader.outerHTML = html;
                // Réinitialiser les liens actifs après le chargement
                setTimeout(() => {
                    updateActiveNavLinks();
                    // Appeler aussi initNavActiveLinks de main.js si elle existe
                    if (window.initNavActiveLinks) {
                        window.initNavActiveLinks();
                    }
                }, 100);
            }
            // Sinon, ajouter au début du body
            else {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const header = tempDiv.querySelector('header');
                if (header) {
                    document.body.insertBefore(header, document.body.firstChild);
                    updateActiveNavLinks();
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement du header:', error);
            // En cas d'erreur, on garde le header existant ou on affiche un message
            if (!existingHeader && !headerContainer) {
                console.warn('Impossible de charger le header. Assurez-vous que le fichier includes/header.html existe.');
            }
        });
}

/**
 * Met à jour les liens actifs dans la navigation selon la page actuelle
 */
function updateActiveNavLinks() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const currentHash = window.location.hash;
    
    // Sélectionner tous les liens de navigation (desktop et mobile)
    const navLinksDesktop = document.querySelectorAll('.lux-nav-link');
    const navLinksMobile = document.querySelectorAll('#mobileMenu a');
    
    // Retirer tous les états actifs
    navLinksDesktop.forEach(link => {
        link.classList.remove('active');
        link.classList.remove('text-white');
        link.classList.add('text-white-50');
    });
    
    navLinksMobile.forEach(link => {
        link.classList.remove('active');
        link.style.borderBottom = '';
        link.style.paddingBottom = '';
        link.style.display = '';
        link.style.width = '';
        link.classList.remove('text-white');
        link.classList.add('text-white-50');
    });
    
    // Gérer le bouton Connexion dans les Actions (desktop)
    const connexionBtnDesktop = document.querySelector('.d-none.d-md-flex a[href="login.html"]');
    if (connexionBtnDesktop) {
        if (currentPage === 'login.html') {
            connexionBtnDesktop.style.borderBottom = '1px solid var(--lux-gold)';
            connexionBtnDesktop.style.paddingBottom = '4px';
            connexionBtnDesktop.classList.remove('text-white-50');
            connexionBtnDesktop.classList.add('text-white');
        } else {
            connexionBtnDesktop.style.borderBottom = '';
            connexionBtnDesktop.style.paddingBottom = '';
            connexionBtnDesktop.classList.remove('text-white');
            connexionBtnDesktop.classList.add('text-white-50');
        }
    }
    
    // Gérer le bouton Connexion dans le menu mobile
    const connexionBtnMobile = document.querySelector('#mobileMenu a[href="login.html"]');
    if (connexionBtnMobile) {
        if (currentPage === 'login.html') {
            connexionBtnMobile.style.borderBottom = '1px solid var(--lux-gold)';
            connexionBtnMobile.style.paddingBottom = '4px';
            connexionBtnMobile.style.display = 'inline-block';
            connexionBtnMobile.style.width = 'fit-content';
            connexionBtnMobile.classList.remove('text-white-50');
            connexionBtnMobile.classList.add('text-white');
        } else {
            connexionBtnMobile.style.borderBottom = '';
            connexionBtnMobile.style.paddingBottom = '';
            connexionBtnMobile.style.display = '';
            connexionBtnMobile.style.width = '';
            connexionBtnMobile.classList.remove('text-white');
            connexionBtnMobile.classList.add('text-white-50');
        }
    }
    
    // Gérer le bouton Réserver dans les Actions (desktop et mobile)
    const reserverBtns = document.querySelectorAll('a[href="villas.html"].btn');
    reserverBtns.forEach(reserverBtn => {
        if (currentPage === 'villas.html') {
            reserverBtn.style.boxShadow = '0 10px 15px -3px rgba(203, 174, 130, 0.4)';
            reserverBtn.style.opacity = '1';
            reserverBtn.style.cursor = 'default';
            reserverBtn.style.pointerEvents = 'none';
        } else {
            reserverBtn.style.boxShadow = '0 10px 15px -3px rgba(203, 174, 130, 0.2)';
            reserverBtn.style.opacity = '';
            reserverBtn.style.cursor = '';
            reserverBtn.style.pointerEvents = '';
        }
    });
    
    // Activer les liens de navigation appropriés
    navLinksDesktop.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        
        let isActive = false;
        
        // Pour la page d'accueil
        if (currentPage === 'index.html' || currentPage === '' || currentPage === '/') {
            if ((href === 'index.html' || href === '/') && !currentHash) {
                isActive = true;
            } else if (currentHash === '#destinations' && (href === 'index.html#destinations' || href === '#destinations')) {
                isActive = true;
            }
        }
        
        // Pour la page villas
        if (currentPage === 'villas.html' && (href === 'villas.html' || href.includes('villas.html'))) {
            isActive = true;
        }
        
        // Pour la page contact
        if (currentPage === 'contact.html' && (href === 'contact.html' || href.includes('contact.html'))) {
            isActive = true;
        }
        
        if (isActive) {
            link.classList.add('active');
            link.classList.remove('text-white-50');
            link.classList.add('text-white');
        }
    });
    
    // Même chose pour le menu mobile
    navLinksMobile.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        
        let isActive = false;
        
        if (currentPage === 'index.html' || currentPage === '' || currentPage === '/') {
            if ((href === 'index.html' || href === '/') && !currentHash) {
                isActive = true;
            }
        } else if (currentPage === 'villas.html' && (href === 'villas.html' || href.includes('villas.html'))) {
            isActive = true;
        } else if (currentPage === 'contact.html' && (href === 'contact.html' || href.includes('contact.html'))) {
            isActive = true;
        }
        
        if (isActive) {
            link.classList.add('active');
            link.style.borderBottom = '1px solid var(--lux-gold)';
            link.style.paddingBottom = '4px';
            link.style.display = 'inline-block';
            link.style.width = 'fit-content';
            link.classList.remove('text-white-50');
            link.classList.add('text-white');
        }
    });
}

// Charger le header au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadHeader();
    
    // Après le chargement du header, appeler initNavActiveLinks de main.js
    setTimeout(() => {
        if (window.initNavActiveLinks) {
            window.initNavActiveLinks();
        }
    }, 300);
});

// Exporter les fonctions pour usage manuel si nécessaire
window.loadHeader = loadHeader;
window.updateActiveNavLinks = updateActiveNavLinks;
