/**
 * LUX ÎLES - JavaScript Principal
 * Fonctionnalités interactives
 */

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Attendre que le header soit chargé avant d'initialiser les liens actifs
    setTimeout(function() {
        initNavActiveLinks();
    }, 200);
    initSmoothScrollToSection();
    initCalendar();
    initChat();
    initFilters();
    initTables();
    initImageUpload();
    // Le header et footer sont chargés automatiquement par leurs loaders respectifs
});

// Exporter la fonction pour usage par header-loader.js
window.initNavActiveLinks = initNavActiveLinks;

// Gestion des liens actifs dans la navigation
function initNavActiveLinks() {
    const currentPath = window.location.pathname;
    const currentHash = window.location.hash;
    
    // Sélectionner tous les liens de navigation (desktop et mobile)
    const navLinksDesktop = document.querySelectorAll('.lux-nav-link');
    const navLinksMobile = document.querySelectorAll('#mobileMenu a');
    const allNavLinks = [...navLinksDesktop, ...navLinksMobile];
    
    allNavLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        
        // Retirer les classes actives existantes pour les liens desktop
        if (link.classList.contains('lux-nav-link')) {
            link.classList.remove('active');
            link.classList.remove('text-white');
            if (!link.classList.contains('active')) {
                link.classList.add('text-white-50');
            }
        }
        
        // Retirer les classes actives pour les liens mobile
        if (link.closest('#mobileMenu')) {
            link.classList.remove('active');
            link.classList.remove('text-white');
            link.classList.add('text-white-50');
        }
        
        // Vérifier si c'est le lien actif
        let isActive = false;
        
        // Extraire le chemin de l'URL du lien (sans le domaine)
        let linkPath = href;
        let linkHash = '';
        
        // Séparer le hash du chemin si présent
        if (href.includes('#')) {
            const parts = href.split('#');
            linkPath = parts[0];
            linkHash = '#' + parts[1];
        }
        
        // Si c'est une URL complète, extraire le pathname
        try {
            if (href.startsWith('http://') || href.startsWith('https://')) {
                const linkUrl = new URL(href);
                linkPath = linkUrl.pathname;
                linkHash = linkUrl.hash;
            } else if (href.startsWith('/')) {
                // C'est déjà un chemin relatif, on l'utilise tel quel
                linkPath = href.split('#')[0];
            }
        } catch (e) {
            // Si l'URL est invalide, utiliser href tel quel
            linkPath = href.split('#')[0];
        }
        
        // Normaliser les chemins (enlever le slash final sauf pour la racine)
        const normalizePath = (path) => {
            if (path === '/' || path === '') return '/';
            return path.replace(/\/$/, '') || '/';
        };
        
        const normalizedCurrentPath = normalizePath(currentPath);
        const normalizedLinkPath = normalizePath(linkPath);
        
        // Pour la page d'accueil (route /)
        if (normalizedCurrentPath === '/') {
            // Si on a un hash dans l'URL actuelle, activer le lien correspondant
            if (currentHash === '#destinations') {
                // Lien Destinations actif si hash #destinations
                if (linkHash === '#destinations' || href.includes('#destinations')) {
                    isActive = true;
                }
            }
            else if (currentHash === '#contact') {
                // Lien Contact actif si hash #contact
                if (linkHash === '#contact' || href.includes('#contact')) {
                    isActive = true;
                }
            }
            // Si pas de hash dans l'URL, activer uniquement le lien Accueil (sans hash)
            else if (!currentHash) {
                // Lien Accueil actif uniquement si le lien pointe vers / sans hash
                if (normalizedLinkPath === '/' && !linkHash) {
                    isActive = true;
                }
            }
        }
        
        // Pour la page villas (route /villas)
        if (normalizedCurrentPath === '/villas' || currentPath.startsWith('/villas/')) {
            if (normalizedLinkPath === '/villas' || linkPath.startsWith('/villas')) {
                isActive = true;
            }
        }
        
        // Pour la page espace client (route /espace-client)
        if (normalizedCurrentPath === '/espace-client' || currentPath.startsWith('/espace-client/')) {
            if (normalizedLinkPath === '/espace-client' || linkPath.startsWith('/espace-client')) {
                isActive = true;
            }
        }
        
        // Pour la page contact (route /contact)
        if (normalizedCurrentPath === '/contact' || currentPath.startsWith('/contact/')) {
            if (normalizedLinkPath === '/contact' || linkPath.startsWith('/contact')) {
                isActive = true;
            }
        }
        
        // Appliquer le style actif
        if (isActive) {
            // Pour les liens desktop
            if (link.classList.contains('lux-nav-link')) {
                link.classList.add('active');
                link.classList.remove('text-white-50');
                link.classList.add('text-white');
            }
            // Pour les liens mobile
            if (link.closest('#mobileMenu')) {
                link.classList.add('active');
                link.classList.remove('text-white-50');
                link.classList.add('text-white');
            }
        }
    });
    
    // Gérer le lien "Connexion" dans la section Actions
    // Sélectionner tous les liens /login dans le header, puis filtrer
    const allConnexionLinks = document.querySelectorAll('header a[href*="/login"]');
    const connexionLinkDesktop = Array.from(allConnexionLinks).find(link => !link.closest('#mobileMenu'));
    const connexionLinkMobile = document.querySelector('#mobileMenu a[href*="/login"]');
    
    if (connexionLinkDesktop) {
        if (currentPath === '/login' || currentPath.startsWith('/login')) {
            connexionLinkDesktop.classList.remove('text-white-50');
            connexionLinkDesktop.classList.add('text-white');
            connexionLinkDesktop.style.borderBottom = '1px solid var(--lux-gold)';
            connexionLinkDesktop.style.paddingBottom = '4px';
        } else {
            connexionLinkDesktop.classList.remove('text-white');
            connexionLinkDesktop.classList.add('text-white-50');
            connexionLinkDesktop.style.borderBottom = '';
            connexionLinkDesktop.style.paddingBottom = '';
        }
    }
    
    if (connexionLinkMobile) {
        if (currentPath === '/login' || currentPath.startsWith('/login')) {
            connexionLinkMobile.classList.remove('text-white-50');
            connexionLinkMobile.classList.add('text-white');
            connexionLinkMobile.style.borderBottom = '1px solid var(--lux-gold)';
            connexionLinkMobile.style.paddingBottom = '4px';
            connexionLinkMobile.style.display = 'inline-block';
            connexionLinkMobile.style.width = 'fit-content';
        } else {
            connexionLinkMobile.classList.remove('text-white');
            connexionLinkMobile.classList.add('text-white-50');
            connexionLinkMobile.style.borderBottom = '';
            connexionLinkMobile.style.paddingBottom = '';
            connexionLinkMobile.style.display = '';
            connexionLinkMobile.style.width = '';
        }
    }
    
    // S'assurer que le lien "Accueil" recharge toujours la page
    // Pas besoin d'écouter les clics, on laisse le navigateur faire son travail naturellement
    // Le lien pointe vers index.html sans hash, donc il recharge toujours la page
    
    // Écouter les changements de hash pour mettre à jour et scroller
    window.addEventListener('hashchange', function() {
        const currentPath = window.location.pathname;
        const currentHash = window.location.hash;
        
        // Si le hash est retiré (vide), activer "Accueil" et scroller en haut
        if ((currentPath === '/' || currentPath === '') && !currentHash) {
            // Scroller en haut de la page
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            
            // Mettre à jour les liens actifs pour activer "Accueil"
            setTimeout(initNavActiveLinks, 100);
            return;
        }
        
        // Si un hash est présent, scroller vers la section
        if ((currentPath === '/' || currentPath === '') && currentHash) {
            // Scroller vers la section après un court délai
            setTimeout(function() {
                scrollToSection(currentHash, true);
            }, 100);
        }
        
        setTimeout(initNavActiveLinks, 100);
    });
    
    // Mettre à jour lors du scroll pour les sections (uniquement sur la page d'accueil)
    if (currentPath === '/' || currentPath === '') {
        // Si on est sur la page d'accueil SANS hash, ne pas activer la détection par scroll
        // Le trait reste toujours sous "Accueil"
        if (!currentHash) {
            // Pas de listener de scroll, "Accueil" reste actif en permanence
            return;
        }
        
        // Si on a un hash (Destinations ou Contact), activer la détection par scroll
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateActiveSection();
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        // Appel initial pour les sections si on a un hash
        setTimeout(updateActiveSection, 500);
    }
}

// Mettre à jour la section active lors du scroll
// Cette fonction n'est appelée que si on a un hash dans l'URL (Destinations ou Contact)
function updateActiveSection() {
    const currentPath = window.location.pathname;
    const currentHash = window.location.hash;
    const scrollY = window.scrollY || window.pageYOffset;
    const navLinksDesktop = document.querySelectorAll('.lux-nav-link');
    const navLinksMobile = document.querySelectorAll('#mobileMenu a');
    const allNavLinks = [...navLinksDesktop, ...navLinksMobile];
    
    // Détecter quelle section est visible
    const sections = document.querySelectorAll('section[id]');
    let currentSection = '';
    const scrollPosition = scrollY + 150; // Offset pour le header fixe
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            currentSection = section.getAttribute('id');
        }
    });
    
    // Si aucune section n'est détectée, ne rien changer
    // (le hash dans l'URL détermine quel lien doit être actif)
    if (!currentSection) {
        return;
    }
    
    // Appliquer l'active sur la section détectée
    allNavLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        
        // Retirer l'active de tous les liens
        if (link.classList.contains('lux-nav-link')) {
            link.classList.remove('active');
            link.classList.remove('text-white');
            link.classList.add('text-white-50');
        }
        
        if (link.closest('#mobileMenu')) {
            link.classList.remove('active');
            link.classList.remove('text-white');
            link.classList.add('text-white-50');
        }
        
        // Activer la section correspondante
        if (currentSection && (href.includes(`#${currentSection}`) || href === `#${currentSection}`)) {
            if (link.classList.contains('lux-nav-link')) {
                link.classList.add('active');
                link.classList.remove('text-white-50');
                link.classList.add('text-white');
            }
            if (link.closest('#mobileMenu')) {
                link.classList.add('active');
                link.classList.remove('text-white-50');
                link.classList.add('text-white');
            }
        }
    });
}

// Initialiser le scroll automatique vers les sections avec hash
function initSmoothScrollToSection() {
    const currentPath = window.location.pathname;
    const currentHash = window.location.hash;
    
    // Si on est sur la page d'accueil avec un hash au chargement, scroller vers la section
    if ((currentPath === '/' || currentPath === '') && currentHash) {
        // Fonction pour scroller après que tout soit chargé
        function doScroll() {
            // Mettre à jour les liens actifs d'abord
            initNavActiveLinks();
            
            // Puis scroller vers la section
            setTimeout(() => {
                scrollToSection(currentHash, true);
            }, 300);
        }
        
        // Attendre que la page soit complètement chargée
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(doScroll, 300);
        } else {
            window.addEventListener('load', function() {
                setTimeout(doScroll, 300);
            });
        }
        
        // Aussi essayer après un délai supplémentaire au cas où
        setTimeout(doScroll, 500);
    }
    
    // Gérer les clics sur les liens avec hash (Destinations, Contact)
    const hashLinks = document.querySelectorAll('a[href*="#destinations"], a[href*="#contact"]');
    hashLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Extraire le hash de l'URL
            const hashMatch = href.match(/#(.+)$/);
            if (hashMatch) {
                const hash = '#' + hashMatch[1];
                const currentPath = window.location.pathname;
                
                // Si on n'est pas déjà sur la page d'accueil, laisser le navigateur charger la page
                // Le scroll se fera automatiquement via l'événement hashchange
                if (currentPath !== '/' && currentPath !== '') {
                    // Laisser le navigateur charger la page d'accueil avec le hash
                    return;
                }
                
                // Si on est déjà sur la page d'accueil, gérer le scroll immédiatement
                if (currentPath === '/' || currentPath === '') {
                    e.preventDefault();
                    
                    // Mettre à jour l'URL avec le hash
                    window.location.hash = hash;
                    
                    // Mettre à jour les liens actifs immédiatement
                    setTimeout(() => {
                        initNavActiveLinks();
                    }, 50);
                    
                    // Scroll vers la section
                    setTimeout(() => {
                        scrollToSection(hash, true);
                    }, 100);
                }
            }
        });
    });
}

// Fonction pour scroller vers une section
function scrollToSection(hash, updateNav = true) {
    if (!hash) return;
    
    const targetId = hash.substring(1); // Retirer le #
    const targetSection = document.getElementById(targetId);
    
    if (targetSection) {
        // Calculer la position avec offset pour le header fixe
        const headerHeight = 80; // Hauteur du header fixe
        const targetPosition = targetSection.offsetTop - headerHeight;
        
        // Scroll smooth vers la section
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
        
        // Mettre à jour les liens actifs après le scroll si demandé
        if (updateNav) {
            setTimeout(() => {
                initNavActiveLinks();
            }, 600); // Attendre que le scroll soit terminé
        }
    } else {
        // Si la section n'est pas encore disponible, réessayer après un délai
        setTimeout(() => {
            scrollToSection(hash, updateNav);
        }, 200);
    }
}

// Calendrier
function initCalendar() {
    const calendars = document.querySelectorAll('.lux-calendar');
    calendars.forEach(calendar => {
        const days = calendar.querySelectorAll('.calendar-day');
        days.forEach(day => {
            day.addEventListener('click', function() {
                if (!this.classList.contains('reserved')) {
                    days.forEach(d => d.classList.remove('selected'));
                    this.classList.add('selected');
                }
            });
        });
    });
}

// Chat
function initChat() {
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chat-messages');
    
    if (chatInput && chatSend && chatMessages) {
        chatSend.addEventListener('click', function() {
            const message = chatInput.value.trim();
            if (message) {
                sendMessage(message);
                chatInput.value = '';
            }
        });
        
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                chatSend.click();
            }
        });
    }
}

function sendMessage(message) {
    const chatMessages = document.getElementById('chat-messages');
    if (!chatMessages) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message sent';
    messageDiv.innerHTML = `
        <div class="mb-1">${message}</div>
        <small class="text-muted">${new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</small>
    `;
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Filtres
function initFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });
}

function applyFilters() {
    console.log('Filtres appliqués');
}

// Tables avec tri
function initTables() {
    const sortHeaders = document.querySelectorAll('.sortable');
    sortHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const index = Array.from(this.parentElement.children).indexOf(this);
            const isAsc = this.classList.contains('sort-asc');
            
            sortHeaders.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            this.classList.add(isAsc ? 'sort-desc' : 'sort-asc');
            
            rows.sort((a, b) => {
                const aText = a.children[index].textContent.trim();
                const bText = b.children[index].textContent.trim();
                return isAsc ? bText.localeCompare(aText) : aText.localeCompare(bText);
            });
            
            rows.forEach(row => tbody.appendChild(row));
        });
    });
}

// Upload d'images
function initImageUpload() {
    const uploadInputs = document.querySelectorAll('.image-upload-input');
    uploadInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const files = e.target.files;
            const preview = this.closest('.upload-area').querySelector('.upload-preview');
            
            if (files.length > 0 && preview) {
                preview.innerHTML = '';
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'uploaded-image';
                        img.style.width = '100px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '4px';
                        img.style.margin = '0.5rem';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    });
}

// Toggle sidebar mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.lux-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

// Confirmation de suppression
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

// Notification toast
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Export des fonctions
window.LuxIles = {
    sendMessage,
    applyFilters,
    toggleSidebar,
    confirmDelete,
    showNotification,
    initImageUpload
};

