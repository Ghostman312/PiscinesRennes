<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Piscine/model/DAOs.php');

function affichageVerification($code)
{
  $codeObj = CodeDAO::readFromCode($code);
  if (null !== $codeObj) {
    $reservation = ReservationDAO::readFromId($codeObj->getIdReservation());
    $vente = VenteDAO::readFromId($codeObj->getIdVente());
    $formule = FormuleDAO::readFromId($vente->getIdFormule());
    $semaine = array(
      " Dimanche ", " Lundi ", " Mardi ", " Mercredi ", " Jeudi ",
      " vendredi ", " samedi "
    );
    $mois = array(
      1 => " janvier ", " février ", " mars ", " avril ", " mai ", " juin ",
      " juillet ", " août ", " septembre ", " octobre ", " novembre ", " décembre "
    );
    switch (CodeDAO::verify($codeObj)) {
      case 1:
        $reponse = "Le code est périmé";
        break;
      case 0:
        $tableau = array(
          array("Code : ", $code),
          array("Réservation", "pas de réservation"),
          array("Date d'achat : ", date("d-m-Y", $vente->getDateCommande())),
          array("Date de péremption : ", date("d-m-Y", $vente->getDatePeremption())),
          array("Nombres de commandes : ", $vente->getNbCommandes()),
          array("Type de formule : ", $formule->getNom())
        );
        echo '<p> N\'hésitez pas à réserver un créneau dans l\'onglet réservation</p>';
        $reponse = $tableau;
        break;
      case 2:
        $creneau = CreneauDAO::readFromId($reservation->getIdCreneau());
        $bassin = BassinDAO::readFromId($creneau->getIdBassin());
        $piscine = PiscineDAO::readFromId($bassin->getIdPiscine());
        $tableau = array(
          array("Code : ", $codeObj->getCode()),
          array("État du code : ", "R   éservé le " . $semaine[date('w', $creneau->getDateFinCours())] .
            date(' d ', $creneau->getDateDebutCours()) .
            $mois[date('n', $creneau->getDateFinCours())] .
            date(' \\d\\e G\\hi', $creneau->getDateFinCours()) .
            ' à ' . date('G\\hi', $creneau->getDateFinCours()) . ' pour le bassin ' . $bassin->getDescription() .
            ' de la piscine ' . $piscine->getNom()),
          array("Date d'achat : ", date("d-m-Y", $vente->getDateCommande())),
          array("Date de péremption : ", date("d-m-Y", $vente->getDatePeremption())),
          array("Nombres de commandes : ", $vente->getNbCommandes()),
          array("Type de formule : ", $formule->getType())
        );
        $reponse = $tableau;
        break;
    }
  } else {
    $reponse = "Code invalide";
  }
  return $reponse;
}

if (isset($_POST['code'])) {
  $response = affichageVerification($_POST['code']);
  if (is_array($response)) {
    echo '<table style="margin: auto; border-collapse: collapse;">';
    foreach ($response as $row) {
      echo '<tr>';
      foreach ($row as $cell) {
        echo '<td style="border: 1px solid black; padding: 10px;">' . $cell . '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
  } else {
    echo '<p>' . $response . '</p>';
  }
  exit;
}
