
<?php

//
// Function that takes a product code (and quantity) as a parameter and adds 
// it to the basket (the basket is stored and retained for the current SESSION)
//

function addToBasket($code, $quantity, $dbConnect)
{
	$result=mysqli_query($dbConnect,"SELECT * FROM products WHERE code='$code'");

	while($productByCode=mysqli_fetch_array($result))
	{
		$itemArray = array($productByCode["code"]=>array('name'=>$productByCode["name"], 'code'=>$productByCode["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode["price"], 'image'=>$productByCode["image"]));
		if(!empty($_SESSION["basket_item"]))
		{
			if(in_array($productByCode["code"],array_keys($_SESSION["basket_item"])))
			{
				foreach($_SESSION["basket_item"] as $k => $v)
				{
					if($productByCode["code"] == $k)
					{
						if(empty($_SESSION["basket_item"][$k]["quantity"]))
						{
							$_SESSION["basket_item"][$k]["quantity"] = 0;
						}
						$_SESSION["basket_item"][$k]["quantity"] += $quantity;
					}
				}
			} 
			else
			{
				$_SESSION["basket_item"] = array_merge($_SESSION["basket_item"],$itemArray);
			}
		}
		else
		{
			$_SESSION["basket_item"] = $itemArray;
		}
	}
}


//
// Function that takes a product code and removes it from the basket
//

function removeFromBasket($code)
{
	foreach($_SESSION["basket_item"] as $k => $v)
	{
		if($code == $k)
			unset($_SESSION["basket_item"][$k]);
	
		if(empty($_SESSION["basket_item"]))
			unset($_SESSION["basket_item"]);
	}

}


//
// Function that returns the total cost of the basket, taking into account
// the delivery and offer rules that are stored together with the products
// in the catalogue database
//

function basketTotal($dbConnect)
{
	$basketTotal = 0.0;

	// Calculate the basket total taking account of offers
	if(!empty($_SESSION["basket_item"]))
	{
		$itemPrice = 0;

		foreach ($_SESSION["basket_item"] as $item)
		{
			// Undiscounted total for this line in the basket
			$itemSubtotal = $item["quantity"] * $item["price"];

			// Check for offers on this item and calculate the discount
			$OfferDiscount = 0;

			if($item["quantity"] > 0)
			{
				$code = $item["code"];
				$offers=mysqli_query($dbConnect,"SELECT * FROM offers WHERE code='$code'");
				
				while($offerByCode=mysqli_fetch_array($offers))
				{
					// Does this offer apply?
					if($item["quantity"] >= $offerByCode["number-of-products"])
					{
						// Work out the discount and apply
						$qualifyingNumberOfItems = intdiv($item["quantity"], $offerByCode["number-of-products"]);
						
						// Round up to 2 decimal places
						$singleDiscount = $item["price"] * $offerByCode["discount"];
						$singleDiscount = round($singleDiscount + 0.005, 2);

						$OfferDiscount += $qualifyingNumberOfItems * $singleDiscount;
					}
				}
			}

			// Add to the subTotal minus any discount
			$basketTotal += ($itemSubtotal - $OfferDiscount);
		}

		// Calculate the deliver cost

		if($basketTotal > 0)
		{
			$delivery = mysqli_query($dbConnect, "SELECT * FROM delivery ORDER BY id ASC");

			$foundCorrectCharge = false;

			while($row = mysqli_fetch_array($delivery))
			{
				if(!$foundCorrectCharge)
				{
					if($basketTotal < $row["total-amount"])
					{
						$basketTotal += $row["delivery-amount"];
						$foundCorrectCharge = true;
					}
				}
			}
		}
	}

	return $basketTotal;
}

?>
