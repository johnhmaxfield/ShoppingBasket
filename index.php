<?php
session_start();

require_once("databaseConnection.php");
require_once("basketFunctions.php");


// Basic basket actions

if(!empty($_GET["action"]))
{
	switch($_GET["action"]) 
	{
		// Adding product in basket
		case "add":
			if(!empty($_POST["quantity"]))
			{
				addToBasket($_GET["code"], $_POST["quantity"], $dbConnect);
			}
			break;

		// Removing product from basket
		case "remove":
			if(!empty($_SESSION["basket_item"]))
			{
				removeFromBasket($_GET["code"]);
			}
			break;

		// Empty basket
		case "empty":
			unset($_SESSION["basket_item"]);
			break;	
	}
}

?>



<!-- The main webpage with embedded PHP -->

<HTML>
<HEAD>
<TITLE>Simple PHP Shopping basket</TITLE>
<link href="style.css" type="text/css" rel="stylesheet" />
</HEAD>
<BODY>
<div id="shopping-basket">
<div class="txt-heading"><b>Shopping Basket</b></div>
<a id="btnEmpty" href="index.php?action=empty">Empty basket</a>

<?php
	if(isset($_SESSION["basket_item"]))
	{
    	$total_quantity = 0;
    	$total_price = 0;
?>

<table class="tbl-basket" cellpadding="10" cellspacing="1">
<tbody>
<tr>
	<th style="text-align:left;">Name</th>
	<th style="text-align:left;">Code</th>
	<th style="text-align:right;" width="5%">Quantity</th>
	<th style="text-align:right;" width="10%">Unit Price</th>
	<th style="text-align:right;" width="10%">Price</th>
	<th style="text-align:center;" width="5%">Remove</th>
</tr>	

<?php		
    foreach ($_SESSION["basket_item"] as $item){
        $item_price = $item["quantity"]*$item["price"];
?>

<tr>
	<td> <img src = "<?php echo $item["image"];?>" class="basket-item-image" /> <?php echo $item["name"]; ?> </td>
	<td> <?php echo $item["code"]; ?> </td>
	<td style="text-align:right;">  <?php echo $item["quantity"]; ?> </td>
	<td style="text-align:right;">  <?php echo "$ ".$item["price"]; ?> </td>
	<td style="text-align:right;">  <?php echo "$ ". number_format($item_price,2); ?> </td>
	<td style="text-align:center;"> <a href="index.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"> <img src="images/remove.png" alt="Remove Item" /> </a> </td>
</tr>

<?php
		$total_quantity += $item["quantity"];
		$total_price += ($item["price"]*$item["quantity"]);
		}
?>

<tr>
	<td align="right" colspan="2">Total:</td>
	<td align="right"> <?php echo $total_quantity; ?></td>
	<td align="right" colspan="2"><strong><?php echo "$ ".number_format(basketTotal($dbConnect), 2); ?> </strong> </td>
	<td> </td>
</tr>
</tbody>
</table>

<?php
	} 
	else 
	{
?>

<div class="no-records">Your basket is Empty</div>

<?php 
	}
?>

</div>


<!-- product list ---->

<div id="product-list">
<div class="txt-heading"><b>Products</b></div>	
<a id="btnEmptyList"></a>

<table class="tbl-basket" cellpadding="10" cellspacing="1">
<tbody>
<tr>
	<th style="text-align:left;">Product</th>
	<th style="text-align:left;">Code</th>
	<th style="text-align:right;" width="10%">Unit Price</th>
	<th style="text-align:center;" width="5%">Add</th>
</tr>	

<?php		
 	$product= mysqli_query($dbConnect,"SELECT * FROM products ORDER BY id ASC");
	if (!empty($product)) 
	{ 
		while ($row=mysqli_fetch_array($product)) 
		{
?>

<tr>
	<form method="post" action="index.php?action=add&code=<?php echo $row["code"]; ?>">
	<td style="text-align:left;"><img src="<?php echo $row["image"]; ?>" class="product-image"><?php echo $row["name"]; ?></td>
	<td style="text-align:left;"><?php echo $row["code"]; ?></td>
	<td style="text-align:right;"><?php echo "$ ".$row["price"]; ?></td>
	<td style="text-align:center;"><input type="text" class="product-quantity" name="quantity" value="1" size="2"/><input type="submit" value="Add to Basket" class="btnAddAction"/></td>
</tr>
</form>

<?php
		}
	}
	else 
	{
 		echo "No Products.";
	}
?>

</tbody>
</table>		
<br><br><br>


<!-- Offers list ---->

<div id="offers-list">
<div class="txt-heading"><b>Offers</b></div>	
<a id="btnEmptyList"></a>

<table class="tbl-basket" cellpadding="10" cellspacing="1">
<tbody>
<tr>
	<th style="text-align:left;">Code</th>
	<th style="text-align:left;">Offer</th>
</tr>	

<?php		
 	$offers= mysqli_query($dbConnect,"SELECT * FROM offers ORDER BY id ASC");
	if (!empty($offers)) 
	{ 
		while ($row=mysqli_fetch_array($offers)) 
		{
?>

<tr>
	<td style="text-align:left;"><?php echo $row["code"]; ?></td>
	<td style="text-align:left;"><?php echo $row["description"]; ?></td>
</tr>

<?php
		}
	}
	else 
	{
 		echo "Currently no offers available";
	}
?>

</tbody>
</table>		
<br><br><br>


<!-- Delivery Charges ---->

<div id="delivery-list">
<div class="txt-heading"><b>Delivery Costs</b></div>	
<a id="btnEmptyList"></a>

<table class="tbl-basket" cellpadding="10" cellspacing="1">
<tbody>
<tr>
	<th style="text-align:left;">Order Amount</th>
	<th style="text-align:left;">Delivery Cost</th>
</tr>	


<?php		
 	$delivery= mysqli_query($dbConnect,"SELECT * FROM delivery ORDER BY id ASC");
	if (!empty($delivery)) 
	{ 
		while ($row=mysqli_fetch_array($delivery)) 
		{
?>

<tr>
	<td style="text-align:left;">Orders under $ <?php echo $row["total-amount"]; ?></td>
	<td style="text-align:right;">$ <?php echo $row["delivery-amount"]; ?></td>
</tr>

<?php
		}
?>

<tr>
	<td style="text-align:left;">Orders over of this </td>
	<td style="text-align:right;">$ 0.00 </td>		
</tr>

<?php
	}
	else 
	{
 		echo "There are no deliver charges";
	}
?>

</tbody>
</table>		

</BODY>
</HTML>