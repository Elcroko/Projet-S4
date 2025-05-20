document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('theme-toggle');
    const themeLink = document.getElementById('theme-css');

    // Au chargement de la page, on vérifie si un thème est déjà stocké dans les cookies
    const theme = getCookie('theme');
    if (theme === 'dark') {
        setDarkTheme(); // Si c’est le thème sombre, on l’applique
    }

    // Lorsqu’on clique sur le bouton pour changer de thème
    toggleButton.addEventListener('click', function() {
        if (document.body.classList.contains('dark-mode')) {
            // Si le thème sombre est déjà actif, on repasse en thème clair
            setLightTheme();
            setCookie('theme', 'light', 30); // Stocke la préférence dans un cookie (valable 30 jours)
        } else {
            // Sinon, on active le thème sombre
            setDarkTheme();
            setCookie('theme', 'dark', 30);
        }
    });

    // Applique le thème sombre
    function setDarkTheme() {
        document.body.classList.add('dark-mode');
        document.body.style.backgroundColor = '#121212';
        document.body.style.color = '#ffffff';
    }

    function setLightTheme() {
        document.body.classList.remove('dark-mode');
        document.body.style.backgroundColor = '#365829';
        document.body.style.color = 'white';
    }

    // Crée ou met à jour un cookie
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // Récupère la valeur d’un cookie à partir de son nom
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';'); // On découpe tous les cookies
        for(let i=0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length); // Supprime les espaces
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length); // Retourne la valeur si trouvé
        }
        return null; // Aucun cookie trouvé
    }
});
