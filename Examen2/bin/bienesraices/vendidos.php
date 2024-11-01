<?php
include "../index/header.php";
require "../index/connect.php"; 
$db = connectDB();

$querySellers = "SELECT id, name FROM seller";
$queryProperties = "SELECT id, title FROM propierties";

$sellersResult = mysqli_query($db, $querySellers);
$propertiesResult = mysqli_query($db, $queryProperties);
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_seller = $_POST["id_seller"] ?? null;
    $id_prop = $_POST["id_prop"] ?? null;

   
    if ($id_seller && $id_prop) {
        $checkSeller = "SELECT id FROM seller WHERE id = ?";
        $checkProperty = "SELECT id FROM propierties WHERE id = ?";

        $stmtSeller = mysqli_prepare($db, $checkSeller);
        $stmtProperty = mysqli_prepare($db, $checkProperty);

        mysqli_stmt_bind_param($stmtSeller, "i", $id_seller);
        mysqli_stmt_bind_param($stmtProperty, "i", $id_prop);

        mysqli_stmt_execute($stmtSeller);
        $sellerExists = mysqli_stmt_get_result($stmtSeller);
        

        mysqli_stmt_close($stmtSeller); 

        mysqli_stmt_execute($stmtProperty);
        $propertyExists = mysqli_stmt_get_result($stmtProperty);
        
        mysqli_stmt_close($stmtProperty);

        if (mysqli_num_rows($sellerExists) > 0 && mysqli_num_rows($propertyExists) > 0) {
            $insertQuery = "INSERT INTO sold_properties (id_seller, id_prop) VALUES (?, ?)";
            $stmtInsert = mysqli_prepare($db, $insertQuery);
            mysqli_stmt_bind_param($stmtInsert, "ii", $id_seller, $id_prop);

            if (mysqli_stmt_execute($stmtInsert)) {
                $message = "Sale registered successfully.";
            } else {
                $message = "Error registering the sale: " . mysqli_error($db);
            }
            mysqli_stmt_close($stmtInsert);
        } else {
            $message = "Invalid seller or property ID.";
        }
    } else {
        $message = "Please select both a seller and a property.";
    }
}
?>

<section>
    <h1>Register a Property Sale</h1>
    <div>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <fieldset>
                <legend>Fill all Forms to Register a Sale</legend>
                
                <div>
                    <label for="id_seller">Seller</label>
                    <select id="id_seller" name="id_seller" required>
                        <option value="">Select a Seller</option>
                        <?php while ($seller = mysqli_fetch_assoc($sellersResult)) : ?>
                            <option value="<?php echo $seller['id']; ?>"><?php echo $seller['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="id_prop">Property</label>
                    <select id="id_prop" name="id_prop" required>
                        <option value="">Select a Property</option>
                        <?php while ($property = mysqli_fetch_assoc($propertiesResult)) : ?>
                            <option value="<?php echo $property['id']; ?>"><?php echo $property['title']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <button type="submit">Register Sale</button>
                </div>
            </fieldset>
        </form>
    </div>
</section>