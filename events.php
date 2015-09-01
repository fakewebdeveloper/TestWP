<?php 

?>
<script type="text/javascript">
jQuery(function($){
	//if ($.isFunction(fullCalendar))
	{
		$('#da_calendar').fullCalendar({
			editable: true,
			eventLimit: true,
			events: [
					{
						title: 'All Day Event',
						start: '2015-06-01'
					},
					{
						title: 'Long Event',
						start: '2015-06-07',
						end: '2015-06-10'
					},
					{
						id: 999,
						title: 'Repeating Event',
						start: '2015-06-09T16:00:00'
					},
					{
						id: 999,
						title: 'Repeating Event',
						start: '2015-06-16T16:00:00'
					},
					{
						title: 'Conference',
						start: '2015-06-11',
						end: '2015-06-13'
					},
					{
						title: 'Meeting',
						start: '2015-06-12T10:30:00',
						end: '2015-06-12T12:30:00'
					},
					{
						title: 'Lunch',
						start: '2015-06-12T12:00:00'
					},
					{
						title: 'Meeting',
						start: '2015-06-12T14:30:00'
					},
					{
						title: 'Happy Hour',
						start: '2015-07-12T17:30:00'
					},
					{
						title: 'Dinner',
						start: '2015-07-12T20:00:00'
					},
					{
						title: 'Birthday Party',
						start: '2015-07-13T07:00:00'
					},
					{
						title: 'Click for Google',
						url: 'http://google.com/',
						start: '2015-02-28'
					}
				]
		});
	}
});
</script>
<div id="da_calendar_container" class="da_calendar_container">
	<div id="da_calendar" class="da_calendar">
		
	</div>
</div>