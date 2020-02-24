# ACME Widget Co Shopping Basket Proof of Concept

Following the brief, this folder contains a proof of concept for a simple shopping basket. The system is designed around a MySQL database containing the list of products, offers and delivery costs. The shopping basket application uses this database, together with PHP functions and embedded scripts to display an interactive shopping basket, a catalogue of products and offers available, and the delivery costs.

## The Catalogue Database

The database that sits behind the application has three tables:

- **Products** : containing the product name, code, image and unit price
- **Offers** : containing a description of the offer, the product code it relates to, the number of products that need to be purchased to qualify for the offer and the discount given on the unit price.
- **Delivery** : containing the order amount below which a specific delivery charge is made and the amount of that charge

The file &quot;catalogue.sql&quot; contains the definition of these records and data to populate the database based on the information provided in the brief.

Before trying to use the shopping basket, the database needs to be created and populated with some initial data.

To do this please start up MySQL server, open the Admin page and create a new database called &quot;catalogue&quot; and then import the &quot;catalogue.sql&quot; file into the database.

## Running Shopping Basket Application

The folder on Github contains several files:

- **index.php** – the main entry page containing both HTML and embedded PHP
- style.css – a basic style sheet for several elements of the webpage
- **basketFunctions.php** – the main implementation of the Add, Total and Remove functions for the basket implemented in PHP
- **databaseConnection.php** – a very short PHP script to open the catalogue database – if the database is called something other than &quot;catalogue&quot; then name can by changed in this file and the rest of the application will still work.
- **Readme.rtf** – this file
- **Images** – a folder containing some simple jpeg and png images for the products and UI

Once the database has been created and set up, as described above, the shopping basket application can be run by simply copying the folder to your local Apache htdocs folder and opening [http://localhost/shopping-basket](http://localhost/shopping-basket) in a local web browser window. Make sure Apache server is running locally as well.

## Implementation

The application is a simple implementation of a shopping basket system. The basket itself is created as a global SESSION array of &quot;basket items&quot;, where each item contains the product name, code, quantity, unit price and image. The &quot;basket items&quot; array is managed by the addToBasket and removeFromBasket functions. The total cost of the basket is calculated by the basketTotal function, taking account of the offers and delivery costs in the catalogue database.

### Page Layout and Structure

The basket is displayed as a table at the top of the page. An embedded PHP script is used to loop through all items in the &quot;basket items&quot; array and add each as a separate row in the table. The final row displays the quantity of all items and total cost, which is returned from the &quot;basketTotal&quot; function.

Each row of the table has a remove button, that when pressed, will call the &quot;removeFromBasket&quot; function with the product code. This will remove the entire row with that product code. There isn&#39;t currently an option on the page to remove just one item at a time.

The basket table also has an &quot;Empty Basket&quot; button above it that will simply delete (unset) the &quot;basket item&quot; array.

The next table on the webpage is the catalogue of available products. This also uses an embedded PHP script to extract the list of products from the catalogue database and display the information in the table.

Each row also has an &quot;Add to Basket&quot; button and an input box so the user can add more than one item to the basket at time. When the add button is pressed the &quot;addToBasket&quot; function is called with the product code and quantity to be added to the basket.

All of the actions on the main page (Add, Remove and Empty) are managed by a simple switch statement in a PHP script at the top of the index.php file (lines 10 – 35)

Following the basket and product tables, there are two further tables that list the available offers and rules governing the delivery costs. Once again, these use embedded PHP scripts to extract and display the list of offers and delivery costs directly from the catalogue database.

### Basket Functions

#### addToBasket

- **Parameters** : product code, quantity, database connection
- **Return value** : none

This function adds items to the basket. It starts by extracting the product details from the database using the product code. It then copies the data to create a new entry for the &quot;basket item&quot;. If the product is already in the array then it simply increments the quantity of the product in the existing entry. Otherwise it merges the new entry to the &quot;basket item&quot; array, or just adds it if the array is empty.

#### removeFromBasket

- **Parameters** : product code
- **Return value** : none

This function searches the &quot;basket item&quot; array for an item matching the product code. If found, the item is deleted (unset). If this is the last item in the &quot;basket item&quot; array, then array itself is also deleted (unset).

#### basketTotal

- **Parameters** : database connection
- **Return value** : total cost of the order, taking account of offers and delivery charges

This function loops through the items in the &quot;basket items&quot; array and creates a subtotal for each line item. The list of offers in the catalogue database is then extracted and checked to see if any of them apply for the product code and quantity in each line item. If the offer applies, then the discount is calculated and subtracted from the subtotal for each line.

The offer identifies the number needed to qualify (&quot;number-of-products&quot;) and the amount of discount to apply (&quot;discount&quot;).

For the example, the offer on R01 is &quot;buy one, get a second half price&quot;. Therefore, every two R01 items in the basket qualify for the discount of 0.5x of the unit price of R01. So, if there are 4 R01 items in the basket, the user will 1x the unit price of R01 discounted. 6 items would by 1.5x, 8 would be 2x, and so forth.

**NOTE:** The assumption here is that the offer applies for EVERY two R01 items that are bought and NOT just for the first two.

Once the discount has been deducted the delivery cost can be calculated. First the list of delivery cost rules is extracted from the catalogue database. For each rule, the basket total is compared to the &quot;total-amount&quot;. If it is below this then the correct delivery cost for that amount is added to the total.

For example, say the deliver list contains two rules:

1. &quot;total-amount&quot; = $50 and deliver cost = $4.95
2. &quot;total-amount&quot; = $90 and deliver cost = $2.95

If the basket total is $15.90, then first rule is checked and we find $15.90 is less than $50, so the deliver charge is $4.95.

If the basket total was $70, then first rule is checked and we find $70 is not less than $50, so rule 2 is checked and we find $70 is less than $90, so the delivery charge is $2.95

If the basket total is $150, then both the first and second rules don&#39;t apply and there are no more rules and so the assumption is there are no delivery charges.

The final basket total taking account of the offers and deliver costs is then returned.

If you have any further questions regarding this POC system please contact John Maxfield ([johnhmaxfield@gmail.com](mailto:johnhmaxfield@gmail.com)).

JHM 24.02.2020