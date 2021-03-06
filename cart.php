<?php
    session_start();
    require_once('models/products.php');
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['id']) {
        $movies = product_get_byid($_POST['id']);
		if ($_POST['quantity'] && is_numeric($_POST['quantity']) &&
			$_POST['quantity'] > 0 && $movies && $movies['stock'] > 0 &&
			$_POST['quantity'] <= $movies['stock']) {
            $basket = unserialize($_SESSION['basketMovie']);
            if ($basket[$_POST['id']]) {
                $basket[$_POST['id']] += $_POST['quantity'];
                $_SESSION['basketCount'] += $_POST['quantity'];
                $_SESSION['basketPrice'] += $movies['price'] * $_POST['quantity'];
            } else {
                $basket[$_POST['id']] = $_POST['quantity'];
                $_SESSION['basketCount'] += $_POST['quantity'];
                $_SESSION['basketPrice'] += $movies['price'] * $_POST['quantity'];
            }
            $_SESSION['basketMovie'] = serialize($basket);
        } else {
            header('Location: movie.php?id='.$_POST['id'].'&toast=Out of stock');
            exit();
        }
    }
    if ($_GET['remove'] == '1') {
        $_SESSION['basketMovie'] = null;
        $_SESSION['basketPrice'] = null;
        $_SESSION['basketCount'] = null;
    }
	$basket = unserialize($_SESSION['basketMovie']);
	if ($_GET['removemovie'])
	{
		$movies = product_get_byid($_GET['removemovie']);
		$_SESSION['basketCount'] -= $_GET['removecount'];
		$_SESSION['basketPrice'] -= $movies['price'] * $_GET['removecount'];
		unset($basket[$_GET['removemovie']]);
		$_SESSION['basketMovie'] = serialize($basket);
	}
?>
<html lang="en">
	<?php $page_name="Cart"; include('components/header.php'); ?>
	<body class="wrapper">
		<?php include('components/nav.php'); ?>
		<main class="cart">
			<h1 class="title">My Cart</h1>
			<?php
			if ($basket) {
				?>
				<table class="basket">
					<thead>
					<tr>
						<td>ID</td>
						<td>Name</td>
						<td></td>
						<td>Price</td>
						<td>Quantity</td>
						<td>Total TTC</td>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($basket as $k => $v) {
						$movies = product_get_byid($k);
						?>
						<tr>
							<td><a href="movie.php?id=<?php echo $k; ?>"><?php echo $k; ?></a></td>
							<td><a href="movie.php?id=<?php echo $k; ?>"><img
										src="http://image.tmdb.org/t/p/w185/<?php echo $movies['picture']; ?>"
										alt=""></a>
							</td>
							<td class="title"><a
									href="movie.php?id=<?php echo $k; ?>"><?php echo $movies['name']; ?></a>
							</td>
							<td><?php echo number_format($movies['price'], 2); ?> €</td>
							<td><?php echo $v ?></td>
							<td><?php echo number_format($movies['price'] * $v, 2); ?> €</td>
							<td>
								<a href='cart.php?removemovie=<?php echo $k; ?>&removecount=<?php echo $v; ?>' class='button'>Remove</a>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="5"></td>
						<td><?php echo isset($_SESSION['basketPrice']) ? $_SESSION['basketPrice'] : '0.00'; ?>
							€
						</td>
					</tr>
					</tfoot>
				</table>
				<div class="checkout">
					<div>
						<a href='cart.php?remove=1' class='button'>Cancel cart</a>
					</div>
					<div>
						<?php
							if ($_SESSION['username']) {
								echo "<form method=\"post\" action=\"controllers/orders.php\" />
								<input type=\"hidden\" name=\"from\" value=\"cart\" />
								<input type=\"hidden\" name=\"success\" value=\"account\" />
								<input type=\"submit\" class='button' value='Checkout'/></form>";
							} else {
								echo "<a href='login.php' class='button'>Login to validate order</a>";
							}
						?>
					</div>
				</div>
				<?php
			} else {
				echo "<h4>Your cart is empty</h4>";
			}
			?>
			</main>
		<?php include('components/footer.php'); ?>
	</body>
</html>
