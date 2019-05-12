<?php
session_start();
include 'classes/autoload.php';
if(isset($_GET['appurl']) && !empty($_GET['appurl'])){
    if (strpos($_GET['appurl'], 'PORTAL_URL') !== false) {
        $apptitle = $_GET['appurl'];
    }
}
$appstatus = $Auth->checkStatus(5);
if($appstatus == "NOT_OK"){
    header("Location: PORTAL_URL");
    exit();
}


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Ermis - Πύλη Ψηφιακών Εφαρμογών</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.iconmonstr.com/1.3.0/css/iconmonstr-iconic-font.min.css">
    <link rel="stylesheet" href="index.css" >
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-light bg-light" id="navbar-main">
    <a class="navbar-brand" href="http://apps.3gel.network" style="font-size: 17px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M14 9v2h-4v-2c0-1.104.897-2 2-2s2 .896 2 2zm10 3c0 6.627-5.373 12-12 12s-12-5.373-12-12 5.373-12 12-12 12 5.373 12 12zm-8-1h-1v-2c0-1.656-1.343-3-3-3s-3 1.344-3 3v2h-1v6h8v-6z"/></svg> Pithia/Σύστημα Ταυτοποίησης
    </a>
</nav>
<div class="jumbotron">
    <h4 style="text-align: center;">Για να εισέλθετε στην εφαρμογή πρέπει πρώτα να ταυτοποιηθείται.</h4><br>
    <h5 style="text-align: center;"><?php echo $apptitle;?></h5>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12.451 17.337l-2.451 2.663h-2v2h-2v2h-6v-5l6.865-6.949c1.08 2.424 3.095 4.336 5.586 5.286zm11.549-9.337c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zm-3-3c0-1.104-.896-2-2-2s-2 .896-2 2 .896 2 2 2 2-.896 2-2z"/></svg> Με κωδικούς Pithia</h5>
                    <p class="card-text">Χρησιμοποιήστε το email και τον κωδικό πρόσβαση σας.</p>
                    <?php
                        if(isset($_POST['pithia-simple-login'])){
                            if(!empty($_POST['pithia-username']) && !empty($_POST['pithia-password'])){

                                $username = $_POST['pithia-username'];
                                $password = $_POST['pithia-password'];
                                if(isset($_GET['appurl']) && !empty($_GET['appurl'])){
                                    $appurl = $_GET['appurl'];
                                    $return_auth = $Auth->Login($username, $password, $appurl);
                                }else{
                                    $return_auth = $Auth->Login($username, $password);
                                }
                                switch ($return_auth){
                                    case "OK":
                                        //Access Granted
                                        if(isset($_GET['appurl']) && !empty($_GET['appurl'])){
                                            $appurl = $_GET['appurl'];
                                            $email = $_SESSION['uid'];
                                            if($appurl != "" || $appurl != null){
                                                $checkperms_response = $Auth->checkPerms($email, $appurl);
                                                switch ($checkperms_response){
                                                    case "PERM_OK":
                                                        header("Location: ".$appurl);
                                                        break;
                                                    case "PERM_NOT":
                                                        ?>
                                                        <div class="alert alert-danger" role="alert" style="width: 100%;">
                                                            Δεν έχετε δικαίωμα πρόσβασης στην εφαρμογή <?php echo $appurl; ?>
                                                        </div>
                                                        <?php
                                                        break;
                                                    case "ERROR_URL":
                                                        ?>
                                                        <div class="alert alert-danger" role="alert" style="width: 100%;">
                                                            Κάτι πήγε στραβά, παρακαλώ δοκιμάστε ξανά (AUTH_URL_ERROR).
                                                        </div>
                                                        <?php
                                                        break;
                                                }
                                            }else{
                                                header("Location: PORTAL_URL");
                                                exit();
                                            }

                                        }else{
                                            header("Location: PORTAL_URL");
                                            exit();
                                        }
                                        break;
                                    case "ERROR_URL":
                                        ?>
                                        <div class="alert alert-danger" role="alert" style="width: 100%;">
                                            Κάτι πήγε στραβά, παρακαλώ δοκιμάστε ξανά (AUTH_URL_ERROR).
                                        </div>
                                        <?php
                                        break;
                                    case "LOCKED":
                                        ?>
                                        <div class="alert alert-danger" role="alert" style="width: 100%;">
                                           Ο λογαριασμός σας είναι κλειδωμένος παρακαλώ επικοινωνήστε με τον διαχειριστή του συστήματος (AUTH_ACC_BLOCKED).
                                        </div>
                                        <?php
                                        break;
                                    case "SYSTEM_LOCK":
                                        ?>
                                        <div class="alert alert-info" role="alert" style="width: 100%;">
                                            Αυτην την στιγμή γίνεται συντήρηση στο σύστημα, παρακαλώ δοκιμάστε αργότερα (AUTH_OUT_OF_ORDER).
                                        </div>
                                        <?php
                                        break;
                                    case "DENIED":
                                        ?>
                                        <div class="alert alert-danger" role="alert" style="width: 100%;">
                                            Το όνομα χρήστη η ο κωδικός πρόσβασης είναι λανθασμένος, παρακαλώ δοκιμάστε ξανά (AUTH_INVALID_CREDS).
                                        </div>
                                        <?php
                                        break;
                                }
                            }else{
                                ?>
                                <div class="alert alert-info" role="alert" style="width: 100%;">
                                    Παρακαλώ συμπληρώστε τα πεδία σύνδεσης και δοκιμάστε ξανά.
                                </div>
                                <?php
                            }
                        }
                    ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="text" class="form-control" name="pithia-username" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Κωδικός Πρόσβασης</label>
                            <input type="password" class="form-control" name="pithia-password" placeholder="Κωδικός Πρόσβασης">
                        </div>
                        <button type="submit" class="btn btn-primary" name="pithia-simple-login">Ταυτοποίηση</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-6" disabled="disabled">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M17.736 9.767c-.538-.374-.467-.28-.67-.886-.085-.253-.33-.425-.605-.425h-.002c-.663.003-.549.038-1.084-.338-.111-.079-.243-.118-.375-.118s-.264.039-.375.118c-.539.379-.422.341-1.084.338h-.002c-.275 0-.521.172-.605.425-.203.608-.133.514-.67.886-.169.117-.264.302-.264.495l.031.19c.208.607.208.49 0 1.095l-.031.191c0 .192.095.378.264.496.537.372.468.278.67.886.085.253.33.424.605.424h.002c.663-.002.549-.038 1.084.338.111.078.243.118.375.118s.264-.04.375-.118c.535-.376.42-.34 1.084-.338h.002c.275 0 .521-.171.605-.424.203-.607.132-.513.67-.886.169-.117.264-.304.264-.496l-.031-.19c-.208-.608-.207-.491 0-1.095l.031-.191c0-.193-.095-.379-.264-.495zm-2.736 2.733c-.828 0-1.5-.671-1.5-1.5s.672-1.5 1.5-1.5 1.5.671 1.5 1.5-.672 1.5-1.5 1.5zm1.065 2.507l.018-.007h.917v7l-1.981-1.285-2.019 1.285v-7h.913c.356.25.592.429 1.087.429.406 0 .799-.174 1.065-.422zm7.935-13.007v18h-5c.099-1.131.387-3.435 3-4.578v-8.844c-1.151-.504-2.074-1.427-2.578-2.578h-14.844c-.504 1.151-1.427 2.074-2.578 2.578v8.844c1.151.504 2.074 1.427 2.578 2.578h6.422v2h-11v-18h24zm-14 9h-5v-1h5v1zm0 1h-5v1h5v-1zm-2 2h-3v1h3v-1zm2-5h-5v-1h5v1z"/></svg> Με πιστοποιητικό υπογεγραμμένο απο Pithia</h5>
                    <p class="card-text">Χρησιμοποιήστε το ψηφιακό πιστοποιητικό που σας έχει δωθεί υπογεγραμμένο απο την Pithia</p>
                    <p>Κατάσταση: <b style="color: red;">Προσωρινά μη διαθέσιμο.</b></p>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
