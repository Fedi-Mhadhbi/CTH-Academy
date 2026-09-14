<?php
session_start();
require_once "../config/db.php";
require_once "../config/log_functions.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
  header("Location: ../../login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="css/darkmode.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
<style>
/* ===== Reset & base ===== */
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { background: #f4f6f9; color:#333; }
header { background:#4b7bec; color:#fff; padding:20px; display:flex; justify-content:space-between; align-items:center; }
.stats { display:flex; gap:20px; padding:20px; justify-content:center; flex-wrap: wrap; }
.card { flex:1; min-width: 200px; background:#fff; padding:20px; border-radius:12px; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
.card span { display: block; font-size: 32px; font-weight: bold; color: #4b7bec; margin-top: 10px; }
table { width:90%; margin:20px auto; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
thead { background:#4b7bec; color:#fff; }
th, td { padding:12px 15px; text-align: left; }
tbody tr:hover { background: #f8f9fa; }
.status-badge { padding:4px 12px; border-radius:12px; color:#fff; font-weight:bold; font-size: 12px; }
button { padding:6px 12px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; margin-right:5px; transition: all 0.3s; }
button.approve { background:#28a745; color:#fff; }
button.approve:hover { background:#218838; transform: translateY(-2px); }
button.delete { background:#dc3545; color:#fff; }
button.delete:hover { background:#c82333; transform: translateY(-2px); }
.active { background-color:#28a745; }
.inactive { background-color:#dc3545; }
.pending { background-color:#ffc107; }
canvas { background:#fff; border-radius:12px; padding:20px; margin:20px auto; display:block; max-width:600px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
h2 { text-align:center; margin-top:40px; color: #4b7bec; }
.loading { text-align: center; padding: 40px; color: #666; }
</style>
</head>
<body>

<header>
<h1>Admin Dashboard</h1>
<a href="../../index.html" style="color:white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 6px;">Logout</a>
</header>

<div class="stats">
<div class="card">
  <div>Teachers</div>
  <span id="teacherCount">0</span>
</div>
<div class="card">
  <div>Students</div>
  <span id="studentCount">0</span>
</div>
<div class="card">
  <div>Courses</div>
  <span id="courseCount">0</span>
</div>
<div class="card">
  <div>Quiz Completion</div>
  <span id="completionRate">0%</span>
</div>
</div>

<canvas id="completionChart"></canvas>

<h2>All Users</h2>
<table>
<thead>
<tr>
<th>#</th>
<th>Full Name</th>
<th>Email</th>
<th>Role</th>
<th>Account</th>
<th>Quiz Attempts</th>
<th>Score</th>
<th>Actions</th>
</tr>
</thead>
<tbody id="userTable">
  <tr><td colspan="8" class="loading">Loading users...</td></tr>
</tbody>
</table>

<h2>Recent Activities</h2>
<table>
<thead>
<tr>
<th>User</th>
<th>Action</th>
<th>Details</th>
<th>Time</th>
</tr>
</thead>
<tbody id="activityTable">
  <tr><td colspan="4" class="loading">Loading activities...</td></tr>
</tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chartInstance = null;

async function loadDashboard(){
  try {
    const res = await fetch("get_dashboard_data.php");
    const data = await res.json();

    // Update stats
    document.getElementById("teacherCount").textContent = data.teachers || 0;
    document.getElementById("studentCount").textContent = data.students || 0;
    document.getElementById("courseCount").textContent = data.courses || 0;
    document.getElementById("completionRate").textContent = (data.completionRate || 0) + "%";

    // Destroy previous chart if exists
    if (chartInstance) {
      chartInstance.destroy();
    }

    // Create new chart
    const ctx = document.getElementById("completionChart").getContext("2d");
    chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending'],
            datasets: [{ 
              data: [data.completed || 0, data.pending || 0], 
              backgroundColor: ['#28a745', '#dc3545'] 
            }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            },
            title: {
              display: true,
              text: 'Quiz Completion Status'
            }
          }
        }
    });

    // Users table
    const table = document.getElementById("userTable");
    table.innerHTML = "";
    
    if (!data.users || data.users.length === 0) {
      table.innerHTML = '<tr><td colspan="8" class="loading">No users found</td></tr>';
      return;
    }

    data.users.forEach(u => {
        let statusText = u.active == 1 ? "Active" : "Pending";
        let statusClass = u.active == 1 ? "active" : "pending";
        
        // Show approve button only for pending users
        let actions = u.active == 0 ? 
            `<button class="approve" onclick="approveUser(${u.id})">✅ Approve</button>
             <button class="delete" onclick="deleteUser(${u.id})">🗑 Delete</button>` :
            `<button class="delete" onclick="deleteUser(${u.id})">🗑 Delete</button>`;

        // Only show quiz data for students
        let attempted = (u.role === "etudiant" || u.role === "student") ? (u.attempted || 0) : "-";
        let total_score = (u.role === "etudiant" || u.role === "student") ? (u.total_score || 0) : "-";

        table.innerHTML += `<tr>
            <td>${u.id}</td>
            <td>${u.nom} ${u.prenom}</td>
            <td>${u.email}</td>
            <td><strong>${u.role}</strong></td>
            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            <td>${attempted}</td>
            <td>${total_score}</td>
            <td>${actions}</td>
          </tr>`;
    });

    // Recent activities
    const actTable = document.getElementById("activityTable");
    actTable.innerHTML = "";
    
    if (!data.activities || data.activities.length === 0) {
      actTable.innerHTML = '<tr><td colspan="4" class="loading">No recent activities</td></tr>';
      return;
    }

    data.activities.forEach(a => {
      actTable.innerHTML += `<tr>
        <td>${a.nom} ${a.prenom}</td>
        <td>${a.action}</td>
        <td>${a.details || '-'}</td>
        <td>${a.created_at}</td>
      </tr>`;
    });

  } catch(error) {
    console.error('Error loading dashboard:', error);
    document.getElementById("userTable").innerHTML = '<tr><td colspan="8" class="loading">❌ Error loading data</td></tr>';
  }
}

async function approveUser(id){
  try {
    const response = await fetch("update_user_status.php", {
      method: "POST", 
      headers: {"Content-Type": "application/json"}, 
      body: JSON.stringify({user_id: id, action: "approve"})
    });
    
    const result = await response.json();
    
    if(result.status === 'success') {
      // Show success message
      alert('✅ User approved successfully!');
      // Reload dashboard to update UI
      await loadDashboard();
    } else {
      alert('❌ Error: ' + (result.message || 'Failed to approve user'));
    }
  } catch(error) {
    console.error('Error:', error);
    alert('❌ Failed to approve user. Please try again.');
  }
}

async function deleteUser(id){
  if(!confirm("Are you sure you want to delete this user?")) return;
  
  try {
    const response = await fetch("update_user_status.php", {
      method: "POST", 
      headers: {"Content-Type": "application/json"}, 
      body: JSON.stringify({user_id: id, action: "delete"})
    });
    
    const result = await response.json();
    
    if(result.status === 'success') {
      alert('✅ User deleted successfully!');
      await loadDashboard();
    } else {
      alert('❌ Error: ' + (result.message || 'Failed to delete user'));
    }
  } catch(error) {
    console.error('Error:', error);
    alert('❌ Failed to delete user. Please try again.');
  }
}

// Load dashboard on page load
loadDashboard();

// Auto-refresh every 30 seconds
setInterval(loadDashboard, 30000);
</script>
<script src="js/darkmode.js"></script>

</body>
</html>