<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Doctor Appointment Scheduling</title>

        <link type="text/css" rel="stylesheet" href="css/layout.css" />

        <!-- DayPilot library -->
        <script src="js/daypilot/daypilot-all.min.js"></script>
    </head>
    <body>
        <?php require_once '_header.php'; ?>

        <div class="main">
            <?php require_once '_navigation.php'; ?>

            <div>

                <div style="float:left; width:160px">
                    <div id="nav"></div>
                </div>
                <div style="margin-left: 160px">
                    <div class="space">Available time slots:</div>
                    <div id="calendar"></div>
                </div>

            </div>
        </div>

        <script src="js/jquery-1.9.1.min.js"></script>
        <script src="js/daypilot/daypilot-all.min.js"></script>

        <script>
            var nav = new DayPilot.Navigator("nav");
            nav.selectMode = "week";
            nav.showMonths = 3;
            nav.skipMonths = 3;
            nav.onTimeRangeSelected = function(args) {
                 //loadEvents(args.start.firstDayOfWeek(), args.start.addDays(7));
				if (calendar.visibleStart().getDatePart() <= args.day && args.day < calendar.visibleEnd()) {
                    calendar.scrollTo(args.day, "fast");  // just scroll
                }
                else {
                    loadEvents(args.day);  // reload and scroll
                }
            };
            nav.init();

            var calendar = new DayPilot.Calendar("calendar");
            calendar.viewType = "week";
            //calendar.timeRangeSelectedHandling = "Disabled";
			calendar.scale = "CellDuration";
			calendar.cellDuration = 30;
            calendar.eventMoveHandling = "Disabled";
            calendar.eventResizeHandling = "Disabled";
            calendar.onBeforeEventRender = function(args) {
				console.log(args.data);
                if (!args.data.tags) {
                    return;
                }
                switch (args.data.tags.status) {
                    case "free":
                        args.data.barColor = "green";
                        args.data.html = args.data.tags.doctor;
                        args.data.toolTip = "Available \n" + args.data.tags.doctor;
                        break;
                    case "waiting":
                        args.data.barColor = "orange";
						args.data.html = "<span style='font-size:9px;'>Patient Name: " + args.data.text+" <br/> Doctor: "+args.data.tags.doctor+"</span>";
                        args.data.toolTip = "Your appointment, waiting for confirmation";
                        break;
                    case "confirmed":
                        args.data.barColor = "#f41616";  // red
						args.data.html = "<span style='font-size:9px;'>Patient: "+args.data.text+" <br/> Doctor: "+args.data.tags.doctor+"</span>";
                        args.data.toolTip = "Your appointment, confirmed";
                        break;
                }
            };
            calendar.onEventClick = function(args) {
                if (args.e.tag("status") !== "free") {
                    // calendar.message("You can only request a new appointment in a free slot.");
                    // return;
					var modal = new DayPilot.Modal({
						onClosed: function(args) {
							if (args.result) {  // args.result is empty when modal is closed without submitting
								loadEvents();
							}
						}
					});

					modal.showUrl("appointment_edit.php?id=" + args.e.id());
                }
				
				if (args.e.tag("status") == "free") {
					var modal = new DayPilot.Modal({
						onClosed: function(args) {
							if (args.result) {  // args.result is empty when modal is closed without submitting
								loadEvents();
							}
						}
					});

					modal.showUrl("appointment_request.php?id=" + args.e.id());
				}
            };
            calendar.init();

            loadEvents();

            function loadEvents(day) {
                var start = nav.visibleStart() > new DayPilot.Date() ? nav.visibleStart() : new DayPilot.Date();

                var params = {
                    start: start.toString(),
                    end: nav.visibleEnd().toString()
                };

                $.post("backend_events_free.php", JSON.stringify(params), function(data) {
                    if (day) {
                        calendar.startDate = day;
                    }
                    calendar.events.list = data;
                    calendar.update();

                    nav.events.list = data;
                    nav.update();

                });
            }
        </script>

    </body>
</html>
