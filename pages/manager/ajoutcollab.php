<?php
session_start();
$titre = 'Ajouter un collaborateur';

include '../../includes/database.php';
include '../../includes/header2.php';
include '../../includes/functions.php';

$data   = [];
$errors = [];
$id = $_SESSION['utilisateur']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = $_POST;
  $RegexMDP = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

  if (empty($data['userLastName']))      $errors['userLastName']   = 'Nom requis';
  if (empty($data['userFirstName']))     $errors['userFirstName']  = 'Prénom requis';
  if (empty($data['userEmail']))         $errors['userEmail']      = 'Email requis';
  if (empty($data['password']))          $errors['password']       = 'Mot de passe requis';
  if ($data['password'] !== $data['confirmPassword']) {
    $errors['confirmPassword'] = 'Les mots de passe ne correspondent pas';
  } elseif (!preg_match($RegexMDP, $data['password'])) {
    $errors['password'] = "Votre mot de passe doit contenir au minimum 8 caractères, 1 lettre majuscule, 1 chiffre et 1 caractère spécial";
  }

  if (empty($data['userEmail'])) {
    $errors['userEmail'] = 'Veuillez renseigner votre email';
  } elseif (filter_var($data['userEmail'], FILTER_VALIDATE_EMAIL) === false) {
    $errors['userEmail'] = 'Votre email est incorrect';
  }
  $checkEmail = $bdd->prepare("SELECT id FROM user WHERE email = :email");
  $checkEmail->execute(['email' => $data['userEmail']]);
  if ($checkEmail->fetch()) {
    $errors['userEmail'] = 'Email déjà utilisé';
  }

  $querryManager_id = $bdd->prepare("SELECT person_id from user where id = :id");
  $querryManager_id->bindParam(':id', $id);
  $querryManager_id->execute();
  $manager_id = $querryManager_id->fetch(pdo::FETCH_ASSOC);

  $querryPosition_id = $bdd->prepare("SELECT id from positions where name = :name ");
  $querryPosition_id->bindParam(':name', $data["userPosition"]);
  $querryPosition_id->execute();
  $position_id = $querryPosition_id->fetch(pdo::FETCH_ASSOC);

  $querryDepartment_id = $bdd->prepare("SELECT id from department where name = :name");
  $querryDepartment_id->bindParam(':name', $data["userDepartment"]);
  $querryDepartment_id->execute();
  $Department_id = $querryDepartment_id->fetch(pdo::FETCH_ASSOC);


  if (empty($errors)) {
    try {
      $bdd->beginTransaction();

      $stmtPerson = $bdd->prepare("
                INSERT INTO person 
                  (last_name, first_name, department_id, position_id, manager_id)
                VALUES 
                  (:last_name, :first_name, :department_id, :position_id, :manager_id)
            ");
      $stmtPerson->execute([
        'last_name'     => $data['userLastName'],
        'first_name'    => $data['userFirstName'],
        'manager_id'    => $manager_id['person_id'],
        'department_id' => $Department_id['id'],
        'position_id'   => $position_id['id']
      ]);
      $person_id = $bdd->lastInsertId();


      $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
      $stmtUser = $bdd->prepare("
                INSERT INTO user 
                  (person_id, email, password, role)
                VALUES 
                  (:person_id, :email, :password, :role)
            ");
      $stmtUser->execute([
        'person_id' => $person_id,
        'email'     => $data['userEmail'],
        'password'  => $hashedPassword,
        'role' => 'collaborateur'
      ]);

      $bdd->commit();


      header('Location: monEquipe1.php');
      exit;
    } catch (Exception $e) {
      $bdd->rollBack();
      echo '<p style="color:red;">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
  }
}

$recupPostes = $bdd->prepare("SELECT name from positions");
$recupPostes->execute();
$postes = $recupPostes->fetchAll(pdo::FETCH_ASSOC);

$requete = $bdd->prepare("
    SELECT name AS department_name
    FROM department
    WHERE id = :id
");

$requete->bindParam(':id', $_SESSION['utilisateur']['departement']);
$requete->execute();

$department = $requete->fetch(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="../../style.css" />

<div class="flex">
  <?php include "../../includes/navBar/navBar2.php"; ?>
  <div class="containerUserDetail page">
    <section class="userDetailSection">
      <h2>Ajouter un collaborateur</h2>

      <form class="userEditForm" method="POST">

        <label for="userEmail">Adresse email <span style="color:red;">*</span></label>
        <input type="email" id="userEmail" name="userEmail"
          value="<?= afficheValeur('userEmail', $data) ?>" required />
        <?= afficheErreur('userEmail', $errors) ?>

        <div class="inlineFields">

          <div class="fieldGroup">
            <label for="userLastName">Nom <span style="color:red;">*</span></label>
            <input type="text" id="userLastName" name="userLastName"
              value="<?= afficheValeur('userLastName', $data) ?>" required />
            <?= afficheErreur('userLastName', $errors) ?>
          </div>

          <div class="fieldGroup">
            <label for="userFirstName">Prénom <span style="color:red;">*</span></label>
            <input type="text" id="userFirstName" name="userFirstName"
              value="<?= afficheValeur('userFirstName', $data) ?>" required />
            <?= afficheErreur('userFirstName', $errors) ?>
          </div>
        </div>

        <div class="inlineFields">

          <div class="fieldGroup">
            <label for="userPosition">Poste <span style="color:red;">*</span></label>
            <select id="userPosition" name="userPosition">
              <?php foreach ($postes as $poste) { ?>
                <option value="<?= $poste["name"] ?>">
                  <?= $poste["name"] ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="fieldGroup">
            <label for="userDepartment">Département</label>
            <input type="text" style="background-color: rgba(243, 244, 246, 1); cursor: not-allowed;" value="<?= $department["department_name"];  ?>" name="userDepartment">
          </div>
        </div>


        <label for="userManager">Manager</label>
        <input name="userManager" style="background-color: rgba(243, 244, 246, 1); cursor: not-allowed;" type="text" value="<?= $_SESSION["utilisateur"]["prenom"] . " " . $_SESSION["utilisateur"]["nom"]; ?>">

        <div class="inlineFields">

          <div class="fieldGroup">
            <label for="password">Mot de passe <span style="color:red;">*</span></label>
            <input type="password" id="password" name="password" required />
            <?= afficheErreur('password', $errors) ?>
          </div>

          <div class="fieldGroup">
            <label for="confirmPassword">Confirmation <span style="color:red;">*</span></label>
            <input type="password" id="confirmPassword" name="confirmPassword" required />
            <?= afficheErreur('confirmPassword', $errors) ?>
          </div>
        </div>

        <div class="btnContainer">
          <button type="submit" class="updateBtn">Créer le collaborateur</button>
        </div>
      </form>
    </section>
  </div>
</div>
</body>

</html>