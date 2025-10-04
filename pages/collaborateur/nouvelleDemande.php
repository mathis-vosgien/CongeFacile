<?php
session_start();
$titre = 'Nouvelle demande';

include "../../includes/database.php";
include "../../includes/header2.php";
include "../../includes/functions.php";
include '../../includes/verifSecuriteCollaborateur.php';


$id = $_SESSION["utilisateur"]['id'];
$data = [];
$errors = [];
$requeteRecupTypeDeConge = $bdd->prepare('SELECT name FROM request_type');
$requeteRecupTypeDeConge->execute();
$TypesConge = $requeteRecupTypeDeConge->fetchAll(PDO::FETCH_ASSOC);

$recupRequetes = $bdd->prepare("SELECT r.start_at, r.end_at from request r where collaborator_id = :id");
$recupRequetes->bindParam(':id', $id);
$recupRequetes->execute();
$Requetes = $recupRequetes->fetchALL(pdo::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = $_POST;

  $RequeteRecupRequest_typeID = $bdd->prepare('SELECT id FROM request_type WHERE name=:name');
  $RequeteRecupRequest_typeID->bindParam(':name', $data['typeDemande']);
  $RequeteRecupRequest_typeID->execute();
  $idRequest_type = $RequeteRecupRequest_typeID->fetch(PDO::FETCH_ASSOC);

  $RequeteRecupDepartment_ID = $bdd->prepare('SELECT department_id FROM person where id=:id');
  $RequeteRecupDepartment_ID->bindParam(':id', $_SESSION['utilisateur']['id']);
  $RequeteRecupDepartment_ID->execute();
  $RecupDepartment_ID = $RequeteRecupDepartment_ID->fetch(PDO::FETCH_ASSOC);



  $data['dateDebut'] = trim($data['dateDebut']);
  $data['dateFin'] = trim($data['dateFin']);
  $data['nbJours'] = trim($data['nbJours']);

  $data['dateDebut'] = htmlspecialchars($data['dateDebut']);
  $data['dateFin'] = htmlspecialchars($data['dateFin']);
  $data['nbJours'] = htmlspecialchars($data['nbJours']);

  if (isset($data['commentaire'])) {
    $data['typeDemande'] = trim($data['typeDemande']);
    $data['commentaire'] = htmlspecialchars($data['commentaire']);
  }

  if (empty($data['typeDemande'])) {
    $errors['typeDemande'] = 'Veuillez renseigner le type de votre demande';
  } else if (filter_var($data['typeDemande'], FILTER_VALIDATE_INT) === true) {
    $errors['typeDemande'] = 'Votre type de demande est incorrect';
  }

  if (empty($data['dateDebut'])) {
    $errors['dateDebut'] = 'Veuillez renseigner votre premier jour de congé';
  }

  if (empty($data['dateFin'])) {
    $errors['dateFin'] = 'Veuillez renseigner votre dernier jour de congé';
  }

  if ($data['dateFin'] <= $data['dateDebut']) {
    $errors['dateFin'] = "Durée de congé incorrecte";
  }

  if (empty($data['nbJours'])) {
    $errors['nbJours'] = 'Veuillez renseigner la durée de votre congé ci-dessus';
  } else if (filter_var($data['nbJours'], FILTER_VALIDATE_INT) === false) {
    $errors['nbJours'] = 'Durée du congé incorrecte';
  }

  $data['dateDebut'] = date('Y-m-d', strtotime($data['dateDebut']));
  $data['dateFin'] = date('Y-m-d', strtotime($data['dateFin']));


  $date = date("Y-m-d H:i:s");
  $collaborateurId = $_SESSION['utilisateur']['id'];
  $request_typeID = $idRequest_type['id'];

  if (empty($errors)) {
    $requeteNouvelleDemande = $bdd->prepare("INSERT INTO request 
      (request_type_id, collaborator_id, department_id, created_at, start_at, end_at, period, receipt_file, comment, answer_comment, answer, answer_at) 
      VALUES (:request_type_id, :collaborator_id, :department_id, :created_at, :start_at, :end_at,:period, :receipt_file,:comment, :answer_comment, NULL, :answer_at)");
    $requeteNouvelleDemande->bindParam(':request_type_id', $request_typeID);
    $requeteNouvelleDemande->bindParam(':collaborator_id', $collaborateurId);
    $requeteNouvelleDemande->bindParam(':department_id', $RecupDepartment_ID['department_id']);
    $requeteNouvelleDemande->bindParam(':created_at', $date);
    $requeteNouvelleDemande->bindParam(':start_at', $data['dateDebut']);
    $requeteNouvelleDemande->bindParam(':end_at', $data['dateFin']);
    $requeteNouvelleDemande->bindParam(':period', $data['nbJours']);
    if (!empty($data['receipt_file'])) {
      $requeteNouvelleDemande->bindValue(':receipt_file', $data['receipt_file']);
    } else {
      $requeteNouvelleDemande->bindValue(':receipt_file', null, PDO::PARAM_NULL);
    }
    if (!empty($data['commentaire'])) {
      $requeteNouvelleDemande->bindValue(':comment', $data['commentaire']);
    } else {
      $requeteNouvelleDemande->bindValue(':comment', null, PDO::PARAM_NULL);
    }
    $requeteNouvelleDemande->bindValue(':answer_comment', null, PDO::PARAM_NULL);
    $requeteNouvelleDemande->bindValue(':answer_at', null, PDO::PARAM_NULL);
    $requeteNouvelleDemande->execute();
    header("Location: historiqueDesDemandes.php");
  }
}




?>




<div class="flex">
  <?php include "../../includes/navBar/navBar1.php"; ?>
  <div class="ContainerNouvelleDemande page">
    <section class="nouvelleDemandeSection">
      <h1>Effectuer une nouvelle demande</h1>
      <form class="nouvelleDemandeForm" method="POST" novalidate >
        <label for="typeDemande">Type de demande</label>
        <select id="typeDemande" name="typeDemande">
          <?php foreach ($TypesConge as $type) { ?>
            <option value="<?= $type["name"] ?>">
              <?= $type["name"] ?>
            </option>
          <?php } ?>
        </select>
        <?= afficheErreur('typeDemande', $errors) ?>

        <div class="inlineFields block">
          <div class="fieldGroup">
            <label for="dateDebut">Date début</label>
            <input type="datetime-local" id="dateDebut" name="dateDebut" required value="<?= afficheValeur('dateDebut', $data)  ?>" />
            <?= afficheErreur('dateDebut', $errors) ?>
          </div>
          <div class="fieldGroup">
            <label for="dateFin">Date de fin</label>
            <input type="datetime-local" id="dateFin" name="dateFin" required value="<?= afficheValeur('dateFin', $data) ?>" />
            <?= afficheErreur('dateFin', $errors) ?>
          </div>
        </div>

        <label for="nbJours">Nombre de jours demandés</label>
        <input class="nbJours" type="number" id="nbJours" name="nbJours" required value="<?= afficheValeur('nbJours', $data) ?>" />
        <?php afficheErreur('nbJours', $errors) ?>

        <label for="justificatif">Justificatif si applicable</label>
        <input type="file" id="justificatif" name="justificatif" value="Sélectionner un fichier" />

        <label for="commentaire">Commentaire supplémentaire</label>
        <textarea id="commentaire" name="commentaire" placeholder="Si congé exceptionnel ou sans solde, vous pouvez préciser votre demande."></textarea>

        <input type="submit" class="submitBtn" value="Soumettre ma demande" />
      </form>

      <p class="ErrorP">* En cas d'erreur de saisie ou de changement, vous pourrez modifier votre demande tant que celle-ci n'a pas été validée par le manager</p>

    </section>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const dateDebut = document.getElementById("dateDebut");
    const dateFin = document.getElementById("dateFin");
    const nbJours = document.getElementById("nbJours");

    function calculerNbJours() {
      const debut = new Date(dateDebut.value);
      const fin = new Date(dateFin.value);

      if (!isNaN(debut) && !isNaN(fin) && fin >= debut) {
        const difference = Math.ceil((fin - debut) / (1000 * 60 * 60 * 24));
        nbJours.value = difference;
      } else {
        nbJours.value = 0;
      }
    }

    dateDebut.addEventListener("change", calculerNbJours);
    dateFin.addEventListener("change", calculerNbJours);
  });

  document.addEventListener("DOMContentLoaded", function() {
    const startDateInput = document.querySelector('input[name="dateDebut"]');
    const endDateInput = document.querySelector('input[name="dateFin"]');
    const daysInput = document.querySelector('input[name="nbJours"]');


    const holidays = [
      "2025-01-01",
      "2025-04-21",
      "2025-05-01",
      "2025-05-08",
      "2025-05-29",
      "2025-06-09",
      "2025-07-14",
      "2025-08-15",
      "2025-11-01",
      "2025-11-11",
      "2025-12-25"
    ];

    function calculateDays() {
      const startDate = new Date(startDateInput.value);
      const endDate = new Date(endDateInput.value);

      if (isNaN(startDate) || isNaN(endDate) || startDate > endDate) {
        daysInput.value = 0;
        return;
      }

      let count = 0;
      let currentDate = new Date(startDate);

      while (currentDate <= endDate) {
        const dayOfWeek = currentDate.getDay();
        const formattedDate = currentDate.toISOString().split('T')[0];

        if (dayOfWeek !== 0 && dayOfWeek !== 6 && !holidays.includes(formattedDate)) {
          count++;
        }


        currentDate.setDate(currentDate.getDate() + 1);
      }

      daysInput.value = count;
    }


    startDateInput.addEventListener("change", calculateDays);
    endDateInput.addEventListener("change", calculateDays);
  });
  Réduire
</script>
<?php
include '../../includes/footer.php';

?>
