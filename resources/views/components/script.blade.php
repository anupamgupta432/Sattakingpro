<script src="js/auth.js"></script>
    <!-- download script -->
     <script>
document.getElementById("exportCsv").addEventListener("click", () => {
  const rows = document.querySelectorAll("#usersTable tr");
  let csv = [];
  rows.forEach(row => {
    const cols = row.querySelectorAll("td, th");
    let rowData = [];
    cols.forEach(col => rowData.push(`"${col.innerText.replace(/"/g, '""')}"`));
    csv.push(rowData.join(","));
  });

  const csvString = csv.join("\n");
  const blob = new Blob([csvString], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.setAttribute("hidden", "");
  a.setAttribute("href", url);
  a.setAttribute("download", "users.csv");
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.getElementById("exportPdf").addEventListener("click", () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  let y = 10;
  const rows = document.querySelectorAll("#usersTable tr");

  rows.forEach(row => {
    const cols = row.querySelectorAll("td, th");
    let rowText = [];
    cols.forEach(col => rowText.push(col.innerText));
    doc.text(rowText.join(" | "), 10, y);
    y += 10;
  });

  doc.save("users.pdf");
});
</script>
<script>
document.getElementById("exportPrint").addEventListener("click", () => {
  const printContents = document.getElementById("usersTable").outerHTML;
  const originalContents = document.body.innerHTML;
  document.body.innerHTML = `
    <html>
      <head><title>Print</title></head>
      <body>${printContents}</body>
    </html>
  `;
  window.print();
  document.body.innerHTML = originalContents;
  location.reload(); // Restore page state
});
</script>
<!-- Download script -->