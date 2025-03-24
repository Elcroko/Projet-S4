<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circuits - Tempus Odyssey</title>
    <link rel="icon" type="image/png" href="images/portail.png">
    <link rel="stylesheet" href="css/circuits.css">
</head>
<body>
    <header>
        <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
        <h1 class="site-title">
            <a href="index.php" style="text-decoration: none; color: inherit;">Tempus Odyssey</a>
        </h1>        
        <nav aria-label="Navigation principale">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="circuits.php">Circuits</a></li>
                <li><a href="inscription.php">Inscription</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="profil.php" class="active">Profil</a></li>                
            </ul>
        </nav>
    </header>
    <main>
        <section class="filters">
            <h2>Recherchez votre voyage temporel</h2>
            <div class="search-container">
                <input type="text" placeholder="🔎 Recherchez une époque ou une destination...">
                <span class="search-icon">🔍</span>
            </div>
            <label for="lieu">Choisissez votre lieu :</label>
            <select id="lieu">
                <option value="terre">🌍 Sur Terre</option>
                <option value="espace">🚀 Dans l'Espace</option>
                <option value="univers-parallele">🔮 Univers Parallèle</option>
            </select>

            <label for="temps-debut"></label>
            <p id="temps-debut-label">Début de la période : -∞</p>
            <input type="range" id="temps-debut" min="0" max="6" step="1" value="0" class="time-slider">
        
            <label for="temps-fin"></label>
            <p id="temps-fin-label">Fin de la période : +∞</p>
            <input type="range" id="temps-fin" min="0" max="6" step="1" value="6" class="time-slider">
        
            <button>Rechercher</button>
        </section>        
        
        <!-- Section des circuits temporels -->
       <section class="featured">
        <h3>Nos circuits temporels</h3>
        <div class="circuits-container">
            <article class="circuit">
                <a href="Mort.html">
                    <img src="images/mort.jpeg" alt="Illustration du circuit Le jour de votre Mort">
                </a>
                <h4>Le Jour de votre Mort</h4>
                <p>Oserez-vous affronter votre destinée et découvrir ce que l’avenir vous réserve ?</p>
            </article>
    
            <article class="circuit">
                <a href="Préhistoire.html">
                    <img src="images/Udino.jpeg" alt="Illustration du circuit La Préhistoire">
                </a>
                <h4>La Préhistoire</h4>
                <p>Évitez les prédateurs préhistoriques et survivez dans un monde sauvage et impitoyable.</p>
            </article>
    
            <article class="circuit">
                <a href="Fin.html">
                    <img src="images/fin_du_monde.jpeg" alt="Illustration du circuit Fin du Monde">
                </a>
                <h4>Fin du Monde</h4>
                <p>Vivez en direct l’apocalypse et assistez aux derniers instants de l’humanité.</p>
            </article>
    
            <article class="circuit">
                <a href="Vikings.html">
                    <img src="images/vinkings.jpeg" alt="Vikings">
                </a>
                <h4>L'Époque des Vikings</h4>
                <p>Rejoignez Ragnar et ses guerriers pour des raids épiques et une conquête sans pitié.</p>
            </article>
            
            <article class="circuit">
                <a href="Chateau.html">
                    <img src="images/chateau.jpeg" alt="chateau">
                </a>
                <h4>À la Cour du Roi Soleil</h4>
                <p>Vivez dans le faste du château de Versailles et assistez aux intrigues royales.</p>
            </article>
    
            <article class="circuit">
                <a href="Bitcoin.html">
                    <img src="images/bitcoin.jpeg" alt="bitcoin">
                </a>
                <h4>L'Ère du Bitcoin</h4>
                <p>Voyagez dans le passé et changez votre destinée financière en maîtrisant la cryptomonnaie.</p>
            </article>
    
            <article class="circuit">
                <a href="Colomb.html">
                    <img src="images/colomb.jpeg" alt="colomb">
                </a>
                <h4>À Bord avec Christophe Colomb</h4>
                <p>Traversez l’Atlantique et assistez à la découverte d’un Nouveau Monde.</p>
            </article>

            <article class="circuit">
                <a href="Pyramide.html">
                    <img src="images/pyramides.jpeg" alt="Construction des Pyramides">
                </a>
                <h4>Le Secret des Pyramides</h4>
                <p>Assistez à la construction des pyramides et découvrez leurs mystères.</p>
            </article>
            
            <article class="circuit">
                <a href="Revolution.html">
                    <img src="images/bastille.jpeg" alt="Prise de la Bastille">
                </a>
                <h4>Révolution à Paris</h4>
                <p>Vivez la prise de la Bastille et plongez en pleine Révolution française.</p>
            </article>
            
            <article class="circuit">
                <a href="Aged'or.html">
                    <img src="images/1ere_gm.jpeg" alt="Première Guerre Mondiale">
                </a>
                <h4>L'Enfer des Tranchées</h4>
                <p>Expérimentez la dure réalité des soldats de la Première Guerre mondiale.</p>
            </article>
            
            <article class="circuit">
                <a href="Résistance.html">
                    <img src="images/2eme_gm.jpeg" alt="Seconde Guerre Mondiale">
                </a>
                <h4>Mission Résistance</h4>
                <p>Rejoignez la Résistance et luttez contre l’occupation nazie.</p>
            </article>
            
            <article class="circuit">
                <a href="Croisière.html">
                    <img src="images/croisiere_interplanetaire.jpeg" alt="Croisière Interplanétaire">
                </a>
                <h4>Croisière Interplanétaire</h4>
                <p>Embarquez pour un voyage à travers les étoiles et explorez les confins de l’univers.</p>
            </article>
            
            <article class="circuit">
                <a href="Olympique.html">
                    <img src="images/jo_3000.jpeg" alt="Jeux Olympiques de l'An 3000">
                </a>
                <h4>Jeux Olympiques de l'An 3000</h4>
                <p>Assistez aux performances incroyables des athlètes du futur dans un stade ultra-technologique.</p>
            </article>          
        </div>
    </section>
    </main>
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
</body>
</html>
