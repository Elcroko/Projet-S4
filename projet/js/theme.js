document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('theme-toggle');
    const themeLink = document.getElementById('theme-css');

    // Vérifier le cookie au chargement
    const theme = getCookie('theme');
    if (theme === 'dark') {
        setDarkTheme();
    }

    toggleButton.addEventListener('click', function() {
        if (document.body.classList.contains('dark-mode')) {
            setLightTheme();
            setCookie('theme', 'light', 30);
        } else {
            setDarkTheme();
            setCookie('theme', 'dark', 30);
        }
    });

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

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i=0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
});
