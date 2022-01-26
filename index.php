<?php
require_once("inc/fonctions.php"); //conection to database
require_once("inc/login.php");

if(isset($_GET['logoff'])){
	unset($_SESSION['logged_in_user_id']);
	unset($_SESSION['logged_in_user_prenom']);
	setcookie("logged_in_user_id","",time() -3600);
	setcookie("logged_in_user_prenom","",time() -3600);


}

if(isset($_GET['choix']))
	{
		$req="SELECT * from produit WHERE categorie = '".$_GET['choix']."' ";
	}	
else if(isset($_GET['produit'])) 
	{
		$req="SELECT * from produit WHERE searchfield LIKE '%".$_GET['produit']."%' ";
	}
else{
	$req="SELECT * from produit";
} 
$resultat = execute_requete($req);
if(isset($_POST['register'])){
	if($_POST['register']=='olduser'){
		if(isset($_POST['email']) && isset($_POST['password']) )
		{
			$requser="SELECT * from client WHERE email = '".$_POST['email']."' AND password = '".$_POST['password']."'";

			$resultatuser = execute_requete($requser);
			$user_actuel = $resultatuser->fetch_assoc();
			//var_dump($user_actuel);
			if (isset($user_actuel['prenom'])) {
				setcookie('logged_in_user_id',$user_actuel['id'],time() +7200);
				setcookie('logged_in_user_prenom',$user_actuel['prenom'],time() +7200);
				$_SESSION['logged_in_user_id'] = $user_actuel['id'];
				$_SESSION['logged_in_user_prenom'] = $user_actuel['prenom'];
			}
			else{
				?><script>$("#pastrouve").modal("show"); </script> <?php
			}	 
		}
	} else if($_POST['register']=='newuser'){
		$reqnewuser="SELECT * from client WHERE email = '".$_POST['email']."'";
		$resultatnewuser = execute_requete($reqnewuser);
		$exist_newuser = $resultatnewuser->fetch_assoc();
		//print_r($resultatnewuser);
		if (isset($exist_newuser['email'])){
			echo "Correo Igual";
			?><script>$("#reg_erroremail").modal("show"); </script> <?php
		}
		else{
			if($_POST['password'] != $_POST['password_2']  ){
				echo "Contrase;a Diferente";
				?><script>$("#reg_errorpassword").modal("show"); </script> <?php
			}
			else{
				$requserinsert = "INSERT INTO client (`id`, `nom`, `prenom`, `email`, `password`, `adresse`, `phone_number`) 
				VALUES (NULL, '".$_POST['nom']."', '".$_POST['prenom']."', '".$_POST['email']."',
				 '".$_POST['password']."', '".$_POST['adresse']."', '".$_POST['phone']."')";
				execute_requete($requserinsert);
				setcookie('logged_in_user_id',$user_actuel['id'],time() +7200);
				setcookie('logged_in_user_prenom',$user_actuel['prenom'],time() +7200);
				$_SESSION['logged_in_user_id'] = $user_actuel['id'];
				$_SESSION['logged_in_user_prenom'] = $user_actuel['prenom'];
			}		
		}
	}
}




	
$contenu = '';
$contenu .= '<div class="boutique-droite">';
while($produit=$resultat->fetch_assoc())
{
      $contenu .= '<div class="boutique-produit">';
		$contenu .= "<h3>$produit[titre_fr]</h3>";
		$contenu .= "<a href=\"fiche_produit.php?id_produit=$produit[id]\"><img src=\"photo/$produit[photo]\" width=\"130\" height=\"100\" /></a>";
		$contenu .= "<p>$produit[prix] €</p>";
		$contenu .= '<a href="fiche_produit.php?id_produit=' . $produit['id'] . '">Voir la fiche</a>';
		$contenu .= '</div>';
     }


 $contenu .= '</div>';


//--------------------------------- AFFICHAGE HTML ---------------------------------//

require_once("inc/haut.inc.php");
echo "<div class='container'>";
require_once("inc/carousel.inc.php");
echo $contenu;
echo "</div>";


