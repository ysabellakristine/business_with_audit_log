NOTES:

Reason behind the pages not being inside a folder:

sir di ko alam bakit pero ayaw gumana ang pages pag nasa loob ng folder kaya linabas ko na lang.

ayaw gumana yung 'pages/index.php' unless yung mismong link nilagay ko parang ganito "http://localhost/mycodes/dream_job_registration/main/handleForms.php?user_id="

otherwise FORBIDDEN 403 error lumalabas

Additions to original code: 

Instead of hard delete, I made a soft delete method so that the audit logs will also include deleted toys and toy resellers in the database.

Added input sanitization and htmlspecialchars
