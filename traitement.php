<?php
require_once("inc/fonctions.php");
require_once("inc/haut.inc.php");
if(isset($_GET["idd"]))
{
      $idproduit =$_GET["idd"];
      supprimerArticle($idproduit);
      //redirection
      header("Location: panier.php");
}
if(isset($_REQUEST["txn_id"]))
{
      echo("<h2 class='text-light'>Transaction réussie</h2>");
      echo("<h3 class='text-light'>Transaction id:".$_REQUEST["txn_id"]."</h3>");
      echo("<h3 class='text-light'>Transaction temp: ".$_REQUEST["payment_date"]."</h3>");
      echo("<h3 class='text-light'>Merci pour votre achat, continuez à utiliser nos services.</h3>");
      session_destroy();

}


if(isset($_GET["param"]))
{
      session_destroy();
      header("Location: panier.php");
}

if(isset($_GET["param2"]))
{

$reqnachat="SELECT max(id) from achat";
$resultatachat = execute_requete($reqnachat);
$achat_actuel = $resultatachat->fetch_assoc();
$n_fact = intval($achat_actuel['max(id)'])+1;
$c=count($_SESSION['panier']['id_produit']);
$total = (montant_global()+round(montant_global()*0.14975,2));

echo("<h2 class='text-light'>Details de la commande</h2>");
echo("<h4 class='text-light'>Commande Numero: ".$n_fact." <h4>");
echo("<h4 class='text-light'>Nombre d'articles commandés:".$c."<h4></br>");
echo("<h3 class='text-light'>Liste des articles:</h3>");
echo("<div style='text-align:center;' ><table class='text-light' style='margin: 0 auto;'>");
for ($i=0; $i <$c ; $i++) {
    echo("<tr><td> Article ".($i+1).":</td><td>".$_SESSION['panier']['titre'][$i]."( ".$_SESSION['panier']['qteProduit'][$i]." )</td>
    <td> $".$_SESSION['panier']['prixProduit'][$i]."</td></tr>");

}
echo("</table></div><br>");
echo("<div style='text-align:center;' ><table class='text-light' style='margin: 0 auto;'>");
echo("<tr><td><h5 class='text-light'>Montant du panier:</h5></td><td> <h5 class='text-light'>$".montant_global()."</h5></td></tr>");

echo("<tr><td><h5 class='text-light'>Frais de livraison:</h5></td><td> <h5 class='text-light'> $0 </h5></td></tr>");

echo("<tr><td><h5 class='text-light'>Taxes:</h5></td><td> <h5 class='text-light'> $".(round(montant_global()*0.14975,2))."</h5></td></tr>");
echo("<tr><td><h5 class='text-light'>Montant total à payer:</h5></td><td> <h3 class='text-light'> 
            $".$total."</h3></td></tr>");
echo("</table></div><br>");
//
//deuxieme partie
//insertion dans la bd
//

$conn=execute_conn();


  $req2= "INSERT INTO achat(id, total, tvq, tvp, time, id_client) 
  VALUES ('".$n_fact."','".$total."','".(round(montant_global()*0.09975,2))."'
  ,'".(round(montant_global()*0.05,2))."',current_timestamp(),'".$_SESSION['logged_in_user_id']."')";


  

if (mysqli_query($conn, $req2)) {
      for ($i=0; $i <$c ; $i++) {
            $req="INSERT INTO achat_produit (id_produit, id_achat, quantite, prix) VALUES ('".$_SESSION['panier']['id_produit'][$i]."', '".$n_fact."'
            ,'".$_SESSION['panier']['qteProduit'][$i]."', '".$_SESSION['panier']['prixProduit'][$i]."')"; 
            mysqli_query($conn, $req);
        }
} else {
      echo "Erreur : " . $req . "<br>" . mysqli_error($conn);
}
//afficher le bouton de paiement 
//require_once("paypal.php");


?>

<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
<input name="amount" type="hidden" value="<?php echo montant_global(); ?>" />
<input name="currency_code" type="hidden" value="CAD" />
<input name="shipping" type="hidden" value="transport" />
<input name="tax" type="hidden" value="<?php echo "".(round(montant_global()*0.14975,2)); ?>" />
<input name="return" type="hidden" value="https://lepicery.000webhostapp.com/traitement.php" />
<input name="cancel_return" type="hidden" value="https://lepicery.000webhostapp.com/traitement.php" />

<input name="notify_url" type="hidden" value="https://lepicery.000webhostapp.com/traitement.php" />

<input name="cmd" type="hidden" value="_xclick" />

<input name="business" type="hidden" value="sb-yevdm2647418@business.example.com" />

<input name="item_name" type="hidden" value="Merci pour ton achat" />

<input name="no_note" type="hidden" value="1" />

<input name="lc" type="hidden" value="CA" />

<input name="bn" type="hidden" value="PP-BuyNowBF" />

<input name="custom" type="hidden" value="12542A" />
<input type="hidden" name="rm" value="2">

<input type="hidden" name="hosted_button_id" value="YHBA79U52ETAA">
<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>


<?php     
} ?>