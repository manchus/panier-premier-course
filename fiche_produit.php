<?php
require_once("inc/fonctions.php");
require_once("inc/haut.inc.php");

if(isset($_GET["id_produit"]))
{ $contenu = '';
    $req="SELECT * from produit WHERE id=".$_GET["id_produit"];
   $resultat= execute_requete($req);
   $produit=$resultat->fetch_assoc();
   
   $contenu .= "<div class='card' style='width: 18rem;'>";
   $contenu .="<img class='card-img-top' src=\"photo/$produit[photo]\" alt='Card image cap'>";
   $contenu .="<div class='card-body'>";
   $contenu .="<h5 class='card-title'> ".strtoupper($produit["titre_fr"])."</h5>";
   $contenu .="<p class='card-text'> ".$produit["description_fr"]."<p>";
   $contenu .="</div>";
   $contenu .="<ul class='list-group list-group-flush'>";
   $contenu .="<li class='list-group-item'> Catégorie: ".$produit["Categorie"]."</li>";
   $contenu .="<li class='list-group-item'> Prix: ".$produit["prix"]." $</li>";
   $contenu .="</ul>";
   //la partie formulaire
   $contenu .="<div class='card-body'>";
   $contenu .="<form name=panier action=fiche_produit.php method=POST>";
   $contenu .="Quantité : <select name=qte>";
   for( $i = 1 ; $i <= intval($produit["stock"]) ; $i++ ){
    $contenu .="<option value=".$i."> ".$i."</option>";
   }
   $contenu .="</select>   ".$produit["unit_achat"]."   ";
   $contenu .="<input type=hidden name=ajout >";
   $contenu .="<input type=hidden name=produit value=".$produit["id"].">";
   if (isset($_SESSION['logged_in_user_id'])) {
            $contenu .="<input type=submit id='image-button' value=' '>";
    }
  else { $contenu .="<input type=submit id='image-button' value=' ' disabled ='true' data-placement='bottom' 
            title='Connectez-vous pour faire vos achats'>";
    }
         
    
   $contenu .="</form>";
   //fin de la partie 
   $contenu .="<a href = index.php> Retour vers la sélection des produits</a><br></div></div>";

  

   echo "<div class='container'>";
   echo $contenu;
   echo "</div>";
}

if(isset($_POST["ajout"]))
{   
    $req="SELECT * from produit WHERE id=".$_POST["produit"];
    $resultat= execute_requete($req);
    $produit=$resultat->fetch_assoc();
    //session_destroy();
    creationPanier();
    ajouterArticle2($produit["id"],$produit["titre_fr"],$_POST["qte"],$produit["prix"]);
    //var_dump($_SESSION['panier']);
    affiche_panier2();
}
else
{
   creationPanier();
   affiche_panier2();     
}