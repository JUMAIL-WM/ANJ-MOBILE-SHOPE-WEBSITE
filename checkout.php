<?php

include 'inc/db.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
  header('location:login.php');
}

if (isset($_POST['order_btn'])) {

  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $number = $_POST['number'];
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $method = mysqli_real_escape_string($conn, $_POST['method']);
  $address = mysqli_real_escape_string($conn, 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code']);
  $placed_on = date('d-M-Y');

  $cart_total = 0;
  $cart_products[] = '';

  $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
  if (mysqli_num_rows($cart_query) > 0) {
    while ($cart_item = mysqli_fetch_assoc($cart_query)) {
      $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
      $sub_total = ($cart_item['price'] * $cart_item['quantity']);
      $cart_total += $sub_total;
    }
  }

  $total_products = implode(', ', $cart_products);

  $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

  if ($cart_total == 0) {
    $message[] = 'Your Cart is empty';
  } else {
    if (mysqli_num_rows($order_query) > 0) {
      $message[] = 'Order already placed!';
    } else {
      mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
      $message[] = 'Order placed successfully!';
      mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    }
  }

}

?>
<?php
include "inc/header.php";
?>

<!-- Header Start -->
<div class="container-fluid bg-primary py-5 mb-5 page-header">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10 text-center">
        <h1 class="display-3 text-white animated slideInDown">Checkout</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a class="text-white" href="{% url 'home' %}">Home</a></li>
            <li class="breadcrumb-item text-white active" aria-current="page">Checkout</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>
<!-- Header End -->



<!-- Checkout Start -->

<div class="container-xxl py-2 wow fadeInUp" data-wow-delay="0.1s">
  <div class="container">
    <div class="py-2">
    <?php
    if (isset($message)) {
      foreach ($message as $message) {
        echo '
      <div class="message alert alert-success d-flex justify-content-between">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
      }
    }
    ?>
    </div>
    <div class="row">

      <div class="col-md-4 order-md-2 mb-4">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">Your cart</span>
          <span class="badge badge-secondary badge-pill">3</span>
        </h4>
        <ul class="list-group mb-3">
          <?php
          $grand_total = 0;
          $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
          if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
              $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
              $grand_total += $total_price;
              ?>
              <li class="list-group-item d-flex justify-content-between lh-condensed">

                <div>
                  <h6 class="my-0">Phone name</h6>
                  <small class="text-muted">
                    <?php echo $fetch_cart['name']; ?>
                  </small>
                </div>
                <span class="text-muted">
                  <?php echo 'Rs.' . $fetch_cart['price'] . '/-' . ' x ' . $fetch_cart['quantity']; ?>
                </span>
              </li>
              <?php
            }
          } else {
            echo '<p class="empty">Your Cart is empty</p>';
          }
          ?>

          <li class="list-group-item d-flex justify-content-between">
            <span>Total</span>
            <strong>Rs.
              <?php echo $grand_total; ?> /-
            </strong>
          </li>
        </ul>


      </div>


      <div class="col-md-8 order-md-1">
        <h4 class="mb-3">Place Your Order</h4>
        <form method="POST">

          <div class="mb-3">
            <label for="Name">Full Name</label>
            <input type="text" class="form-control" id="name" placeholder="Your Name" name="name" required>

          </div>


          <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Email Address" name="email" required>

          </div>


          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="country">Country</label>
              <select class="custom-select d-block w-100 form-control" id="country" required name="country">
                <option value="">Select an option....</option>
                <option value="Sri Lanka">Sri Lanka</option>
              </select>

            </div>



            <div class="col-md-6 mb-3">
              <label for="state">State</label>
              <select class="custom-select d-block w-100 form-control" id="state" name="state" required>
                <option value="">Select an option....</option>
                <option value="SelectState">Select State</option>
                <option value="Ampara">Ampara</option>
                <option value="Batticaloa">Batticaloa</option>
                <option value="Trincomalee">Trincomalee</option>
                <option value="Badulla">Badulla</option>
                <option value="Monaragala">Monaragala</option>
                <option value="Hambantota">Hambantota</option>
                <option value="Matara">Matara</option>
                <option value="Galle">Galle</option>
                <option value="Anuradhapura">Anuradhapura</option>
                <option value="Polonnaruwa">Polonnaruwa</option>
                <option value="Matale">Matale</option>
                <option value="Kandy">Kandy</option>
                <option value="Nuwara Eliya">Nuwara Eliya</option>
                <option value="Kegalle">Kegalle</option>
                <option value="Ratnapura">Ratnapura</option>
                <option value="Jaffna">Jaffna</option>
                <option value="Kilinochchi">Kilinochchi</option>
                <option value="Mannar">Mannar</option>
                <option value="Mullaitivu">Mullaitivu</option>
                <option value="Vavuniya">Vavuniya</option>
                <option value="Puttalam">Puttalam</option>
                <option value="Kurunegala">Kurunegala</option>
                <option value="Gampaha">Gampaha</option>
                <option value="Colombo">Colombo</option>
                <option value="Kalutara">Kalutara</option>
                <option value="Jaffna">Jaffna</option>
                <option value="Kurunegala">Kurunegala</option>
                <option value="Colombo">Colombo</option>
                <option value="Anuradhapura">Anuradhapura</option>
                <option disabled style="background-color:#aaa; color:#fff">Kandy</option>
                <option value="Ratnapura">Ratnapura</option>
                <option value="Trincomalee">Trincomalee</option>
                <option value="Badulla">Badulla</option>
                <option value="Galle">Galle</option>
                <option value="Jaffna">Jaffna</option>
                <option value="Mullaitivu">Mullaitivu</option>
                <option value="Puttalam">Puttalam</option>

              </select>

            </div>

          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="city">Town / City</label>
              <input type="text" class="form-control" id="city" placeholder="City" name="city" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="phone">Phone No.</label>
              <input type="tel" class="form-control" id="phone" placeholder="Mobile Number" name="number" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" placeholder="e.g. Home No." name="flat" required>

          </div>

          <div class="mb-3">
            <label for="address2">Address 2 <span class="text-muted">(Optional)</span></label>
            <input type="text" class="form-control" id="address2" placeholder="e.g. Street Name" name="street">
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="postcode">Postcode</label>
              <input type="number" class="form-control" id="postcode" placeholder="e.g. 123456" name="pin_code"
                required>
            </div>


            <div class="col-md-6 mb-3">
              <label for="country">Payment Method</label>
              <select class="custom-select d-block w-100 form-control" id="country" name="method" required>
                <option value="cash on delivery">Cash On Delivery</option>
                <option value="credit card">Credit Card</option>
                <option value="paypal">Paypal</option>
                <option value="paytm">Paytm</option>
              </select>

            </div>
          </div>

          <button class="btn text-white w-100 p-3" type="submit" name="order_btn">ORDER NOW</button>
        </form>
      </div>

    </div>

  </div>
</div>
<!-- Checkout End -->

<?php
include "inc/footer.php";
?>
