<?php
$page_title = "Customers";
include('header.php');
?>

<h2 class="mb-4">Registered Customers</h2>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Contact No</th>
                    <th>Gender</th>
                    <th>User Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT user_id, name, mail, contact_no, gender, user_type FROM users WHERE user_type = 'customer'";
                $res = mysqli_query($conn, $sql);
                if ($res) {
                    $sn = 1;
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>";
                            echo "<td>" . $sn++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['name'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['mail'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['contact_no'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['gender'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['user_type'] ?? 'N/A') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="6" class="text-center">No registered customers found.</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">Error fetching data: ' . htmlspecialchars(mysqli_error($conn)) . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>
