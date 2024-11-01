<div id="Confirmed" class="tabcontent">
              <h3>Inactive Outlet</h3>
    <table id="outletTable">
        <tr>
            <th>Outlet Name</th>
            <th>Outlet Slogan</th>
            <th>Outlet Menu</th>
            <th>Opening Hour</th>
            <th>Status</th>
            <th>Style</th>
			<th>Action</th>
            <!-- Add more columns as needed -->
        </tr>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "hmis";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT OutletName, OutletSlogan, OutletMenu, `Opening Hour`, Status, Style FROM hoteloutlet where status = 0" ;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["OutletName"] . "</td>";
                echo "<td>" . $row["OutletSlogan"] . "</td>";
                echo "<td>" . $row["OutletMenu"] . "</td>";
                echo "<td>" . $row["Opening Hour"] . "</td>";
                echo "<td>" . $row["Status"] . "</td>";
                echo "<td>" . $row["Style"] . "</td>";
				echo "<td><button class='action-btn review' onclick='openModal(\"" . $row["OutletName"] . "\", \"" . $row["OutletSlogan"] . "\", \"" . $row["OutletMenu"] . "\", \"" . $row["Opening Hour"] . "\", \"" . $row["Status"] . "\", \"" . $row["Style"] . "\")'>Review</button></td>";
          echo "</tr>";
                // Add more columns here
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No outlets found.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</div>
