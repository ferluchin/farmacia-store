<?php
$buys = array();
if(isset($_GET["start_at"]) && isset($_GET["finish_at"])){
$buys =  BuyData::getByRange($_GET["start_at"],$_GET["finish_at"]);

}else{
$buys =  BuyData::getAll();

}
$paymethods = PaymethodData::getAll();
$statuses = StatusData::getAll();

?>
        <!-- Main Content -->

          <div class="row">
          <div class="col-md-12">
          <h1>Reporte de Ventas</h1>
          </div>
          </div>
<form>
<input type="hidden" name="view" value="sellreport">
          <div class="row">
            <div class="col-lg-2">
            <!--<select class="form-control" name="paymethod_id">
              <option> -- METODO --</option>
              <?php foreach($paymethods as $pay):?>
                <option value="<?php echo $pay->id; ?>"><?php echo $pay->name; ?></option>
                <?php endforeach; ?>
            </select>-->
            </div>
            <div class="col-lg-2">
            <!--<select class="form-control" name="status_id">
              <option> -- ESTADO --</option>
              <?php foreach($statuses as $pay):?>
                <option value="<?php echo $pay->id; ?>"><?php echo $pay->name; ?></option>
                <?php endforeach; ?>
            </select>-->
            </div>
            <div class="col-lg-3">
            <input type="date" name="start_at" class="form-control">
            </div>
            <div class="col-lg-3">
            <input type="date" name="finish_at" class="form-control">
            </div>
            <div class="col-md-2">
            <input type="submit" value="Generar" class="btn btn-primary">
            </div>

            </div>
            </form>
<br>
<?php if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!=""&&$_GET["finish_at"]!=""):
$start_at = strtotime($_GET["start_at"]);
$finish_at = strtotime($_GET["finish_at"]);

?>
 <a onclick="javascript:createPDF()"  class="btn btn-primary"><i class="fa fa-download"></i> Descargar</a>
 <br><div class="box box-primary">
<div id="graph" class="animate" data-animate="fadeInUp" ></div>
</div>
<script>

<?php 
echo "var c=0;";
echo "var dates=Array();";
echo "var data=Array();";
echo "var total=Array();";
for($i=$start_at;$i<=$finish_at;$i+=(60*60*24)){
  $operations = BuyData::getAllByDate(date("Y-m-d",$i));
  $total=0;
  foreach ($operations as $buy) {
    $opxs = BuyProductData::getAllByBuyId($buy->id);
    foreach($opxs as $op){
      $product = $op->getProduct();
      $total += ($op->q*$product->price);
    }
  }
//  echo $operations[0]->t;
//  $sl = $operations[0]->t!=null?$operations[0]->t:0;
 // $sp = $spends[0]->t!=null?$spends[0]->t:0;
  echo "dates[c]=\"".date("Y-m-d",$i)."\";";
  echo "data[c]=".$total.";";
  echo "total[c]={x: dates[c],y: data[c]};";
  echo "c++;";
}
?>
// Use Morris.Area instead of Morris.Line
Morris.Area({
  element: 'graph',
  data: total,
  xkey: 'x',
  ykeys: ['y',],
  labels: ['Y']
}).on('click', function(i, row){
  console.log(i, row);
});
</script>
<?php endif;?>
<br>

          <div class="row">
            <div class="col-lg-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <i class="fa fa-tasks"></i> Reporte de Ventas
                </div>
                <div class="widget-body medium no-padding">

                  <div class="table-responsive">
<?php if(count($buys)>0):?>
                    <table class="table table-bordered">
                    <thead>
                      <th></th>
                      <th>Operacion</th>
                      <th>Cliente</th>
                      <th>SubTotal</th>
                      <th>Descuento</th>
                      <th>Total</th>
                      <th>Metodo de pago</th>
                      <th>Estado</th>
                      <th>Fecha</th>
                    </thead>
<?php foreach($buys as $b):
$discount=0;
?>
                        <tr>
                        <td><a href="index.php?view=openbuy&buy_id=<?php echo $b->id; ?>" class="btn btn-xs btn-default">Detalles</a></td>
                        <td>#<?php echo $b->id; ?></td>
                        <td><?php echo $b->getClient()->getFullname(); ?></td>
    <td>$ <?php echo number_format($b->getTotal(),2,".",","); ?></td>
    <td>$
      <?php if($b->coupon_id!=null){
        $coupon = CouponData::getById($b->coupon_id);
        $discount = $coupon->val;
        echo number_format($discount,2,".",",");
        }else{
        echo number_format($discount,2,".",",");

        }
      ?>
    </td>
    <td>$ <?php echo number_format($b->getTotal()-$discount,2,".",","); ?></td>
                        <td><?php echo $b->getPaymethod()->name; ?></td>
                        <td><?php echo $b->getStatus()->name; ?></td>
                        <td><?php echo $b->created_at; ?></td>
                        </tr>
<?php endforeach; ?>
                    </table>



<script type="text/javascript">
        function createPDF() {

var columns = [
    {title: "Operacion", dataKey: "operation"},
    {title: "Cliente", dataKey: "cliente"}, 
    {title: "Subtotal", dataKey: "subtotal"}, 
    {title: "Descuento", dataKey: "descuento"}, 
    {title: "Total", dataKey: "total"}, 
    {title: "Metodo de pago", dataKey: "method"}, 
    {title: "Estado", dataKey: "status"}, 
    {title: "Fecha", dataKey: "created_at" }, 
//    ...
];
var rows = [
  <?php foreach($buys as $s):
$discount=0;
if($s->coupon_id!=null){
        $coupon = CouponData::getById($s->coupon_id);
        $discount = $coupon->val;
        }else{

        }
  ?>

    {
      "operation": "#<?php echo $s->id; ?>",
      "cliente": "<?php echo $s->getClient()->getFullname(); ?>",
      "subtotal": "<?php echo number_format($s->getTotal(),2,".",","); ?>",
      "descuento": "<?php echo number_format($discount,2,".",","); ?>",
      "total": "<?php echo number_format($s->getTotal()-$discount,2,".",","); ?>",
      "method": "<?php echo $s->getPaymethod()->name; ?>",
      "status": "$ <?php echo $s->getStatus()->name; ?>",
      "created_at":"<?php echo $s->created_at ; ?>"
      },
 <?php endforeach; ?>
 //   {"id": 2, "name": "Nelson", "country": "Kazakhstan"},
//    {"id": 3, "name": "Garcia", "country": "Madagascar"},
//    ...
];

// Only pt supported (not mm or in)
var doc = new jsPDF('p', 'pt');

        doc.setFontSize(26);
        doc.text("REPORTE DE VENTAS", 40, 60);
//        doc.text("Header", 40, 30);
  //      doc.text("Header", 40, 30);

doc.autoTable(columns, rows, {
    theme: 'grid',
    overflow:'linebreak',
    styles: {
        fillColor: [100, 100, 100]
    },
    columnStyles: {
        id: {fillColor: 255}
    },
    margin: {top: 70},
    afterPageContent: function(data) {
//        doc.text("Header", 40, 30);
    }
});
//doc.setFontsize

doc.setFontSize(20);
doc.setFontSize(12);
doc.save('report-<?php echo date("d-m-Y h:i:s",time()); ?>.pdf');
//doc.output("datauri");

        }
    </script>
















<?php else:?>
  <div class="panel-body">
  <h1>No hay operaciones</h1>
  </div>
<?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

          </div>
