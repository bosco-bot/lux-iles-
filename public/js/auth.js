/**
 * LUX ÎLES - Authentification API avec Axios
 * Gestion de l'inscription, connexion et déconnexion
 */

// Configuration Axios pour Laravel
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Récupérer le token CSRF depuis la meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
}

// Intercepteur pour gérer les erreurs globales
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // Token CSRF expiré, recharger la page
            window.location.reload();
        }
        return Promise.reject(error);
    }
);

/**
 * Gestion de l'inscription
 */
function handleRegister(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Récupérer les données du formulaire
    const formData = {
        first_name: form.querySelector('#first_name').value.trim(),
        last_name: form.querySelector('#last_name').value.trim(),
        email: form.querySelector('#email').value.trim(),
        password: form.querySelector('#password').value,
        password_confirmation: form.querySelector('#password_confirmation').value,
        phone: form.querySelector('#phone')?.value.trim() || '',
    };
    
    // Validation côté client
    if (!formData.first_name || !formData.last_name || !formData.email || !formData.password) {
        showAuthError('Veuillez remplir tous les champs obligatoires.');
        return;
    }
    
    if (formData.password.length < 8) {
        showAuthError('Le mot de passe doit contenir au moins 8 caractères.');
        return;
    }
    
    if (formData.password !== formData.password_confirmation) {
        showAuthError('Les mots de passe ne correspondent pas.');
        return;
    }
    
    // Désactiver le bouton et afficher le chargement
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Création en cours...';
    
    // Masquer les erreurs précédentes
    hideAuthErrors();
    
    // Appel API
    axios.post('/api/auth/register', formData)
        .then(response => {
            if (response.data.success) {
                showAuthSuccess(response.data.message || 'Compte créé avec succès !');
                
                // Mettre à jour le header immédiatement
                if (response.data.user) {
                    updateHeaderForLoggedInUser(response.data.user);
                }
                
                // Rediriger après un court délai
                setTimeout(() => {
                    window.location.href = response.data.redirect || '/espace-client';
                }, 1500);
            }
        })
        .catch(error => {
            let errorMessage = 'Une erreur est survenue lors de la création du compte.';
            
            if (error.response && error.response.data) {
                const data = error.response.data;
                
                if (data.message) {
                    errorMessage = data.message;
                } else if (data.errors) {
                    // Afficher les erreurs de validation
                    const errors = Object.values(data.errors).flat();
                    errorMessage = errors.join('<br>');
                } else if (data.error) {
                    // Erreur serveur
                    errorMessage = data.error;
                }
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            console.error('Erreur inscription:', error);
            showAuthError(errorMessage);
            
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
}

/**
 * Gestion de la connexion
 */
function handleLogin(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Récupérer les données du formulaire
    const rememberChecked = form.querySelector('#remember')?.checked || false;
    const formData = {
        email: form.querySelector('#email').value.trim(),
        password: form.querySelector('#password').value,
        remember: rememberChecked,
    };
    
    // Validation côté client
    if (!formData.email || !formData.password) {
        showAuthError('Veuillez remplir tous les champs.');
        return;
    }
    
    // Désactiver le bouton et afficher le chargement
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connexion...';
    
    // Sauvegarder l'URL intended si elle existe dans un champ caché
    const intendedInput = document.getElementById('intended-url');
    if (intendedInput && intendedInput.value) {
        sessionStorage.setItem('intended_url', intendedInput.value);
    }
    
    // Masquer les erreurs précédentes
    hideAuthErrors();
    
    // Appel API
    axios.post('/api/auth/login', formData)
        .then(response => {
            if (response.data.success) {
                // Vérifier si l'utilisateur est administrateur
                if (response.data.user && response.data.user.is_admin) {
                    showAuthError('Les administrateurs doivent utiliser la page de connexion administrateur.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    return;
                }
                
                showAuthSuccess(response.data.message || 'Connexion réussie !');
                
                // Mettre à jour le header immédiatement
                if (response.data.user) {
                    updateHeaderForLoggedInUser(response.data.user);
                }
                
                // Vérifier s'il y a une URL intended dans l'URL actuelle (depuis les paramètres)
                const urlParams = new URLSearchParams(window.location.search);
                const intendedUrl = urlParams.get('intended') || sessionStorage.getItem('intended_url');
                
                // Rediriger après un court délai
                setTimeout(() => {
                    // Priorité : URL intended > redirect de la réponse > espace client par défaut
                    const redirectUrl = intendedUrl || response.data.redirect || '/espace-client';
                    if (intendedUrl) {
                        sessionStorage.removeItem('intended_url');
                    }
                    window.location.href = redirectUrl;
                }, 1500);
            }
        })
        .catch(error => {
            let errorMessage = 'Identifiants incorrects.';
            
            if (error.response && error.response.data) {
                const data = error.response.data;
                
                if (data.message) {
                    errorMessage = data.message;
                } else if (data.errors) {
                    // Afficher les erreurs de validation détaillées
                    const errors = Object.values(data.errors).flat();
                    errorMessage = 'Erreur de validation :<br>' + errors.join('<br>');
                }
            }
            
            showAuthError(errorMessage);
            
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
}

/**
 * Gestion de la déconnexion
 */
function handleLogout() {
    axios.post('/api/auth/logout')
        .then(response => {
            if (response.data.success) {
                showAuthSuccess('Déconnexion réussie.');
                setTimeout(() => {
                    window.location.href = response.data.redirect || '/';
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la déconnexion:', error);
            // Forcer la redirection même en cas d'erreur
            window.location.href = '/';
        });
}

/**
 * Afficher un message d'erreur
 */
function showAuthError(message) {
    hideAuthErrors();
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
    errorDiv.setAttribute('role', 'alert');
    errorDiv.innerHTML = `
        <strong>Erreur :</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
    }
}

/**
 * Afficher un message de succès
 */
function showAuthSuccess(message) {
    hideAuthErrors();
    
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success alert-dismissible fade show';
    successDiv.setAttribute('role', 'alert');
    successDiv.innerHTML = `
        <strong>Succès :</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(successDiv, form.firstChild);
    }
}

/**
 * Masquer tous les messages d'erreur/succès
 */
function hideAuthErrors() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => alert.remove());
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Formulaire d'inscription
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
    
    // Formulaire de connexion
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Bouton de déconnexion (desktop)
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleLogout();
        });
    }
    
    // Bouton de déconnexion (mobile)
    const logoutBtnMobile = document.getElementById('logout-btn-mobile');
    if (logoutBtnMobile) {
        logoutBtnMobile.addEventListener('click', function(e) {
            e.preventDefault();
            handleLogout();
        });
    }
    
    // Vérifier l'état de connexion au chargement
    checkAuthStatus();
});

/**
 * Vérifier l'état de connexion et mettre à jour le header
 */
function checkAuthStatus() {
    axios.get('/api/auth/user')
        .then(response => {
            if (response.data.success && response.data.user) {
                updateHeaderForLoggedInUser(response.data.user);
            }
        })
        .catch(error => {
            // Utilisateur non connecté, ne rien faire
            console.log('Utilisateur non connecté');
        });
}

/**
 * Mettre à jour le header pour un utilisateur connecté
 */
function updateHeaderForLoggedInUser(user) {
    const initials = (user.first_name?.[0] || '') + (user.last_name?.[0] || '');
    const avatarContent = user.photo_url 
        ? `<img src="${user.photo_url}" alt="Avatar" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover; border-radius: 50%;"><span class="user-initials d-none">${initials.toUpperCase()}</span>`
        : `<span class="user-initials">${initials.toUpperCase()}</span>`;
    
    // Desktop
    const actionsDesktop = document.querySelector('.d-none.d-md-flex.align-items-center.gap-4');
    if (actionsDesktop) {
        const loginLink = actionsDesktop.querySelector('a[href*="/login"]');
        if (loginLink) {
            const avatarHtml = `
                <div class="user-avatar-dropdown position-relative">
                    <button class="user-avatar-btn border-0 bg-transparent p-0" type="button" id="userAvatarBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-circle position-relative overflow-hidden">
                            ${avatarContent}
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userAvatarBtn" style="min-width: 200px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="small fw-medium text-dark">${user.first_name} ${user.last_name}</div>
                            <div class="small text-muted">${user.email}</div>
                        </li>
                        <li><a class="dropdown-item" href="/espace-client"><i class="far fa-user me-2"></i>Mon espace</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" id="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                    </ul>
                </div>
            `;
            loginLink.outerHTML = avatarHtml;
            
            // Réattacher l'événement de déconnexion
            const newLogoutBtn = document.getElementById('logout-btn');
            if (newLogoutBtn) {
                newLogoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleLogout();
                });
            }
        }
    }
    
    // Mobile
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu) {
        const loginLinkMobile = mobileMenu.querySelector('a[href*="/login"]');
        if (loginLinkMobile) {
            const avatarMobileContent = user.photo_url
                ? `<img src="${user.photo_url}" alt="Avatar" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover; border-radius: 50%;"><span class="user-initials d-none" style="font-size: 0.75rem;">${initials.toUpperCase()}</span>`
                : `<span class="user-initials" style="font-size: 0.75rem;">${initials.toUpperCase()}</span>`;
            
            const avatarMobileHtml = `
                <div class="mt-2 d-flex align-items-center gap-2">
                    <div class="user-avatar-circle position-relative overflow-hidden" style="width: 32px; height: 32px;">
                        ${avatarMobileContent}
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-white fw-medium">${user.first_name} ${user.last_name}</div>
                        <a href="#" class="small text-white-50 text-decoration-none" id="logout-btn-mobile">Déconnexion</a>
                    </div>
                </div>
            `;
            loginLinkMobile.outerHTML = avatarMobileHtml;
            
            // Réattacher l'événement de déconnexion mobile
            const newLogoutBtnMobile = document.getElementById('logout-btn-mobile');
            if (newLogoutBtnMobile) {
                newLogoutBtnMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleLogout();
                });
            }
        }
    }
}

// Export pour usage global
window.AuthAPI = {
    handleRegister,
    handleLogin,
    handleLogout,
    showAuthError,
    showAuthSuccess,
    checkAuthStatus,
    updateHeaderForLoggedInUser
};

