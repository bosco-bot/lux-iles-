/**
 * LUX ÎLES - Footer Loader
 * Charge le footer de manière réutilisable sur toutes les pages
 */

/**
 * Charge le footer dans un élément avec l'id "footer-container" ou remplace un footer existant
 */
function loadFooter() {
    const footerPath = 'includes/footer.html';
    const footerContainer = document.getElementById('footer-container');
    const existingFooter = document.querySelector('footer.lux-footer');
    
    fetch(footerPath)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            // Si un container existe, l'utiliser
            if (footerContainer) {
                footerContainer.innerHTML = html;
            }
            // Sinon, remplacer le footer existant
            else if (existingFooter) {
                existingFooter.outerHTML = html;
            }
            // Sinon, ajouter à la fin du body
            else {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const footer = tempDiv.querySelector('footer');
                if (footer) {
                    document.body.appendChild(footer);
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement du footer:', error);
            // En cas d'erreur, on garde le footer existant ou on affiche un message
            if (!existingFooter && !footerContainer) {
                console.warn('Impossible de charger le footer. Assurez-vous que le fichier includes/footer.html existe.');
            }
        });
}

// Charger le footer au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadFooter();
});

// Exporter la fonction pour usage manuel si nécessaire
window.loadFooter = loadFooter;

