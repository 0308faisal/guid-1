<!-- BEGIN SIDEBAR -->
<div class="page-sidebar" id="main-menu">
    <!-- BEGIN MINI-PROFILE -->
    <div class="page-sidebar-wrapper" id="main-menu-wrapper">
		<!-- END MINI-PROFILE -->
		<!-- BEGIN SIDEBAR MENU -->

		<ul>
      <?php
      if($user->admin>0){?>
			<li>Guidedoc Community
				<ul>
					<li> <a href='community_access.php'>- Community Access Management</a></li>
				</ul>
			</li>
      <?php }
      if($user->manager>0){?>
			<li>Network Management
				<ul>
					<li> <a href='index.php'>- Guideline Management</a></li>
					<li> <a href='guideline_edit.php'>- Create Guideline</a></li>
          <li> <a href='network_access.php'>- Network Access Management</a></li>
					<li> <a href='network_settings.php'>- Network Settings</a></li>
					<li> <a href='member_management.php'>- Member Management</a>
						<ul>
							<li> <a href='approvals.php'>- - Approvals</a></li>
						</ul>
					</li>
				</ul>
			</li>
    <?php }
    if($user->admin=="1"){?>
			<li>Guidedoc Admin
				<ul>
					<li> <a href='user_management.php'>- User Management</a></li>
					<li> <a href='network_management.php'>- Network Management</a></li>
					<li> <a href='change_log.php'>- Change Log</a></li>
				</ul>
			</li>
    <?php }?>
		</ul>

		<!-- END SIDEBAR MENU -->
    </div>
</div>
<!-- END SIDEBAR -->
