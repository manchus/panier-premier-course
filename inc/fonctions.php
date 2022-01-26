<?php
session_start();
function creationPanier(){
   if (!isset($_SESSION['panier'])){
      $_SESSION['panier']=array();
      $_SESSION['panier']['id_produit'] = array();
      $_SESSION['panier']['titre'] = array();
      $_SESSION['panier']['qteProduit'] = array();
      $_SESSION['panier']['prixProduit'] = array();
      $_SESSION['panier']['verrou'] = false;
   }
   return true;
}

function ajout($idproduit,$titre,$qte,$prix)
{
	$position=array_search($idproduit, $_SESSION['panier']['id_produit']);
	if($position==false)
		{
			array_push($_SESSION['panier']['id_produit'],$idproduit);
			array_push($_SESSION['panier']['titre'],$titre);
			array_push($_SESSION['panier']['qteProduit'],$qte);
			array_push($_SESSION['panier']['prixProduit'],$prix);
		}
	else
		{
		 $_SESSION['panier']['qteProduit'][$position]+=$qte;
		}
}

function ajouterArticle2($idproduit,$titre,$qteproduit,$prixproduit){
     creationPanier();
      $kk =-1;
      foreach ($_SESSION['panier']['id_produit'] as $key => $value) {
         echo "KEy:".$key;
      	if($value==$idproduit)
      		{
            $kk = $key;
      		}   
      }
      if($kk ==-1)
      	{
         array_push( $_SESSION['panier']['id_produit'],$idproduit);
         array_push( $_SESSION['panier']['titre'],$titre);
         array_push( $_SESSION['panier']['qteProduit'],$qteproduit);
         array_push( $_SESSION['panier']['prixProduit'],$prixproduit);
      	}
      	else
      		{
      			$_SESSION['panier']['qteProduit'][$kk] += $qteproduit ;

      		}

}

function execute_conn()
{ 
   $mysqli=new mysqli("localhost","root","","store");   
   //$mysqli=new mysqli("localhost","id15507369_root","3}^p%6w<RU*oKIDU","id15507369_store");
   if($mysqli->connect_error) die (" Erreur de connexion");
   return $mysqli;
}


function execute_requete($requete)
{ 
   $mysqli=new mysqli("localhost","root","","store");
   //$mysqli=new mysqli("localhost","id15507369_root","3}^p%6w<RU*oKIDU","id15507369_store");
   if($mysqli->connect_error) die (" Erreur de connexion");
   $resultat=$mysqli->query($requete);
   if(!$resultat)
      { 
         die("Erreur sur la requete SQL")  ;
      } 
   return $resultat;
 }
 
 
function affiche_panier2()
{
   if(isset($_SESSION['panier']['id_produit'])){
      $count=count($_SESSION['panier']['id_produit']);
      echo("<br><br><div class='container'><table class='table table-dark table-hover'");
      echo("<tr><td colspan=5 align=center> Panier d'achat</td></tr>");
      echo("<tr><td>Titre</td><td>Id Produit</td><td >Quantité</td><td >Prix unitaire</td><td >Action</td></tr>");
      for ($i=0; $i <$count ; $i++) { 
         echo "<tr><td>".$_SESSION['panier']['titre'][$i]."</td><td> ".$_SESSION['panier']['id_produit'][$i]."</td><td> ".$_SESSION['panier']['qteProduit'][$i]." </td><td>".$_SESSION['panier']['prixProduit'][$i]."$</td><td> <a href=traitement.php?idd=".$_SESSION['panier']['id_produit'][$i]."><input type=submit name=supprimer value=supprimer></a> </td></tr>";
      }
      echo("<tr><td colspan=3 align=center> Total</td><td colspan=2> ".montant_global()." $</td></tr>");
      echo("<tr><td colspan=5 align=center> <a href=traitement.php?param2=valider><input type=submit name=valider value=valider et declarer le paiement> </a></td></tr>");
      echo("<tr><td colspan=5 align=center> <a href=traitement.php?param=delete> Vider mon panier</a></td></tr>");
      echo("<tr><td colspan=5 align=center> <a href=index.php> Retourn au magasin</a></td></tr>");
      echo("</table></div>");
   }
   else{
      echo("<h2 class='text-light'>Vous n'avez pas d'achat enregistré</h2>");
   }
   

}

function montant_global()
{
   $total=0;
   $count=count($_SESSION['panier']['id_produit']);
   for ($i=0; $i <$count ; $i++) { 
      $total+=round((float)$_SESSION['panier']['qteProduit'][$i]*(float)$_SESSION['panier']['prixProduit'][$i],2);
   }
   return $total;
}

function supprimerArticle($idproduit)
{
   //Si le panier existe
   creationPanier() ;
      //Nous allons passer par un panier temporaire
      $tmp=array();
      $tmp['id_produit'] = array();
      $tmp['titre'] = array();
      $tmp['qteProduit'] = array();
      $tmp['prixProduit'] = array();
      $tmp['verrou'] = $_SESSION['panier']['verrou'];
      for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
      {   
         if ($_SESSION['panier']['id_produit'][$i] !== $idproduit)
         {
            array_push( $tmp['id_produit'],$_SESSION['panier']['id_produit'][$i]);
            array_push( $tmp['titre'],$_SESSION['panier']['titre'][$i]);
            array_push( $tmp['qteProduit'],$_SESSION['panier']['qteProduit'][$i]);
            array_push( $tmp['prixProduit'],$_SESSION['panier']['prixProduit'][$i]);
         }
      }
      //On remplace le panier en session par notre panier temporaire à jour
      $_SESSION['panier'] =  $tmp;
      //On efface notre panier temporaire
      unset($tmp);
   }

//contenu du fichier index.php
//  require_once("inc/function.inc.php");

// $req="SELECT * from produit";
// $resultat=execute_requete($req);