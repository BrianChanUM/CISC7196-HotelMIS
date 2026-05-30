 <div id="All" class="tabcontent">
              <h3>All</h3>
 <table id="orderbookings">
  <tr>
    <th onclick="sortTable(0)">Order ID</th>
    <th onclick="sortTable(1)">Order Type</th>
    <th onclick="sortTable(2)">Time</th>
    <th onclick="sortTable(3)">Email</th>
    <th onclick="sortTable(4)">Order Remark</th>
    <th onclick="sortTable(5)">Status</th>
    <th onclick="sortTable(6)">Order Created Date</th>
    <th onclick="sortTable(7)">Order Modified Date</th>
    <th>Action</th>
  </tr>
  <?php
          $servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "hmis";

          // Create connection
          $conn = new mysqli($servername, $username, $password, $dbname);

          // Check connection
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }
  $sql = "SELECT * FROM orderbookings ";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["OrderID"] . "</td>";
          echo "<td>" . $row["OrderType"] . "</td>";
          echo "<td>" . $row["Time"] . "</td>";
          echo "<td>" . $row["Email"] . "</td>";
          echo "<td>" . $row["OrderRemark"] . "</td>";
          echo "<td>" . $row["Status"] . "</td>";
          echo "<td>" . $row["OrderCreatedDate"] . "</td>";
          echo "<td>" . $row["OrderModifiedDate"] . "</td>";
          echo "<td><button class='action-btn review' onclick='openModal(\"" . $row["OrderID"] . "\", \"" . $row["OrderType"] . "\", \"" . $row["Time"] . "\", \"" . $row["Email"] . "\", \"" . $row["OrderRemark"] . "\", \"" . $row["Status"] . "\", \"" . $row["OrderCreatedDate"] . "\", \"" . $row["OrderModifiedDate"] . "\")'>Review</button></td>";
          echo "</tr>";
      }
  } else {
      echo "0 results";
  }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST["orderId"];
    $status = $_POST["status"];
    $date = date('Y-m-d H:i:s'); // Get the current date and time

    $sql = "UPDATE orderbookings SET Status='$status', OrderModifiedDate=NOW() WHERE OrderID=$orderId";
    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
  $conn->close();
  ?>
</table></div>