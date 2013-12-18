<?php 
    include_once 'ClassEventsTable.php';
          
    //Create an instance of our package class...
    $testListTable = new Event_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();


?>
<div class="wrap">
    
    <div id="icon-users" class="icon32"><br/></div>
    <h2>Gravitate Event Tracking</h2>
    
    <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
		<p>Gravitate Event Tracking Integration In Progress!</p>
        <h3>Add New Event</h3>
        <form>
        <table>
            <tr>
                <td><input type="text"></td>
                <td><input type="text"></td>
                <td><input type="text"></td>
                <td><input type="text"></td>
            </tr>
        </table>
        <input type="submit">
        <form>
    </div>
    <br />
    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="events-table" method="post" action="tracking">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php $testListTable->display() ?>
    </form>
    
</div>
