<?php
// print_r($_SESSION);
if(!empty($_POST) && isset($_SESSION["client_id"])){
$buy = new BuyData();

$alphabeth ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWYZ1234567890_-";
$code = "";
$k = "";
for($i=0;$i<11;$i++){
    $code .= $alphabeth[rand(0,strlen($alphabeth)-1)];
    $k .= $alphabeth[rand(0,strlen($alphabeth)-1)];
}

$buy->k = $k;
$buy->code = $code;
$buy->coupon_id = isset($_SESSION["coupon"])?$_SESSION["coupon"]:"NULL";
$buy->client_id = $_SESSION["client_id"];
$buy->paymethod_id= $_POST["paymethod_id"];
$buy->status_id= 1;
$b = $buy->add();

foreach ($_SESSION["cart"] as $c) {
	$p = new BuyProductData();
	$p->buy_id = $b[1];
	$p->product_id = $c["product_id"];
	$p->q = $c["q"];
	$p->add();
}

// agregamos un history

$h = new HistoryData();
$h->buy_id = $b[1];
$h->status_id=1;
$h->add();
/////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////// Emailing
$client = ClientData::getById($_SESSION["client_id"]);
$adminemail = 	$paypal_business = ConfigurationData::getByPreffix("general_main_email")->val;
$coin = ConfigurationData::getByPreffix("general_coin")->val;
//if($coin=="€"){ $coin=chr(128); }

$replymessage = '
<meta content="es-mx" http-equiv="Content-Language" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body>
<h2>Tienda en Linea</h2>
<h3>Compra Pendiente</h3>
<p><span class="style3"><strong>Estimado '.( $client->getFullname()) .'</strong></span></p>
<p>Se a agregado una compra a tu lista de pendientes, te invitamos a seguir el procedimiento de pago correspondiente para recibir tus productos.</p>
<p>Gracias por tu compra.</p>
<hr>
<p>Powered By @ferluchin </p>
</body>';

$products = BuyProductData::getAllByBuyId($b[1]);
$data = "";
$total = 0;
foreach ($products as $px) {
	$product = $px->getProduct();
	$data .= "<tr>";
	$data .= "<td>$px->q</td>";
	$data .= "<td>".($product->name)."</td>";
	$data .= "<td> $coin".number_format($product->price,2,".",",")."</td>";
	$data .= "<td> $coin".number_format($px->q*$product->price,2,".",",")."</td></tr>";
	$total+= $px->q*$product->price;
}

$themessage = '
<meta content="es-mx" http-equiv="Content-Language" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body>
<h1>Tienda en linea</h1>
<h3>Nueva compra Pendiente</h3>
<h4>Cliente: '.($client->getFullname()).'</h4>
<table align="center" border=1 cellspacing="4" class="style2" style="width: 700">
	<tr>
		<td>Cant.</td><td>Producto</td><td>P.U</td><td>Total</td>
	</tr>
	'.$data.'
</table>
<h3>Total =  '.$coin.number_format($total,2,".",",").' </h3>
<hr>
<p>Powered By @ferluchin </p>
</body>';

mail("$adminemail",
     "Nueva compra Pendiente",
     "$themessage",
	 "From: $adminemail\nReply-To: $adminemail\nContent-Type: text/html; charset=ISO-8859-1");

mail("$client->email",
     "Nueva compra Pendiente",
     "$replymessage",
	 "From: $adminemail\nReply-To: $adminemail\nContent-Type: text/html; charset=ISO-8859-1");

/////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////


if($_POST["paymethod_id"]==PaymethodData::getByName("paypal")->id){
	$paypal_business = ConfigurationData::getByPreffix("paypal_business")->val;
	$paypal_currency = ConfigurationData::getByPreffix("paypal_currency")->val;
	$paypal_cursymbol = ConfigurationData::getByPreffix("paypal_cursymbol")->val;
	$paypal_location = ConfigurationData::getByPreffix("paypal_location")->val;
	$paypal_returnurl = ConfigurationData::getByPreffix("paypal_returnurl")->val;
	$paypal_returntxt = ConfigurationData::getByPreffix("paypal_returntxt")->val;
	$paypal_cancelurl = ConfigurationData::getByPreffix("paypal_cancelurl")->val;

	// complete the return and cancel URL

	$paypal_returnurl .= "&id=".$b[1]."&k=$k";
	$paypal_cancelurl .= "&id=".$b[1]."&k=$k";


	$ppurl = "https://www.paypal.com/cgi-bin/webscr?cmd=_cart";
	$ppurl .= "&business=".$paypal_business;
	$ppurl .= "&no_note=1";
	$ppurl .= "&currency_code=".$paypal_currency;
	$ppurl .= "&charset=utf-8&rm=1&upload=1";
	$ppurl .= "&business=".$paypal_business;
	$ppurl .= "&return=".urlencode($paypal_returnurl);
	$ppurl .= "&cancel_return=".urlencode($paypal_cancelurl);
	$ppurl .= "&page_style=&paymentaction=sale&bn=katanapro_cart&invoice=KP-$b[1]";
//	echo $ppurl;
	$i=1;
	foreach ($_SESSION["cart"] as $c) {
		$product = ProductData::getById($c["product_id"]);
		$c["product_id"];
		$q = $c["q"];
		$ppurl.="&item_name_$i=".urlencode($product->name)."&quantity_$i=$q&amount_$i=".$product->price."&item_number_$i=";
		$i++;

	}

	$ppurl.= "&tax_cart=0.00";

//	echo urldecode("http%3A%2F%2Flocalhost%2Fwp%2Fcheckout%2Forder-received%2F76%3Fkey%3Dwc_order_567671a554da3%26%23038%3Butm_nooverride%3D1");
//	$ppurl .= "&business=".$paypal_business;
unset($_SESSION["cart"]);
unset($_SESSION["coupon"]);

	Core::redir($ppurl);

}
unset($_SESSION["cart"]);
unset($_SESSION["coupon"]);

Core::redir("index.php?view=client");
}
?>