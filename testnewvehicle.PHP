<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Type Management</title>
    <style>
	table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: #fff;
        }
	
        /* Background overlay for the modal */
        #modal {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Modal content */
        .modal-content {
            background-color: #fefefe; /* White background */
            margin: 15% auto; /* Centered vertically */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Adjust as needed */
            /* You can add more styles here (e.g., box-shadow, font-size, etc.) */
        }
    </style>
</head>
<body>
    <h1>Vehicle Type Management</h1>

    <table>
        <thead>
            <tr>
                <th>Vehicle Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Replace with actual database query results
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "HMIS";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to retrieve vehicle types and status
            $sql = "SELECT vehicletype, status FROM hotelvehicletype";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td class="editable" data-vehicletype="' . $row['vehicletype'] . '">' . $row['vehicletype'] . '</td>';
                    echo '<td class="editable" data-status="' . $row['status'] . '">' . ($row['status'] ? 'Enabled' : 'Disabled') . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="2">No data available</td></tr>';
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <!-- Hidden modal for editing -->
    <div id="modal">
        <div class="modal-content">
            <!-- Add your form fields for editing here -->
            <h2>Edit Vehicle Type</h2>
            <input type="text" id="editVehicleType" placeholder="Enter vehicle type">
            <label for="editStatus">Status:</label>
            <input type="checkbox" id="editStatus">
            <button onclick="saveChanges()">Save Changes</button>
        </div>
    </div>

    <script>
        // Add event listeners for double-click on editable cells
        const editableCells = document.querySelectorAll('.editable');
        editableCells.forEach((cell) => {
            cell.addEventListener('dblclick', (event) => {
                const vehicleType = cell.getAttribute('data-vehicletype');
                const status = cell.getAttribute('data-status');

                // Show the edit modal
                document.getElementById('editVehicleType').value = vehicleType;
                document.getElementById('editStatus').checked = status === '1'; // Convert to boolean

                // Display the modal
                document.getElementById('modal').style.display = 'block';
            });
        });

        // Implement saveChanges function to update the database (AJAX request)
        function saveChanges() {
            // Get edited values from the modal
            const editedVehicleType = document.getElementById('editVehicleType').value;
            const editedStatus = document.getElementById('editStatus').checked;

            // Update the database using AJAX (similar to your existing logic)
            // Refresh the table after updating
            // ...

            // Hide the modal
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>
