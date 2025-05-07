<?php
function accueilControleur($twig){
echo $twig->render('accueil.html.twig', array());
}
function contactControleur(){
    echo 'Contact';
}

function maintenanceControleur($twig){
    echo 'Maintenance';
}

