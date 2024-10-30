# Namensübersicht
Dieser Plugin erzeugt eine Übersicht aller Namen, welche im Forum vertreten sind. Im ACP kann man einstellen, ob man eine Übersicht mit Divers haben möchte oder nur weiblich und männlich. Sowohl Vornamen als auch Nachnamen werden nach dem ABC sortiert und in Abschnitte aufgeteilt. Es ist zudem Möglich die Nachnamen über ein Profilfeld auslesen zu lassen (wenn man z.B. mehrere Vornamen zulässt und wirklich nur die Nachnamen auslesen möchte).

## Adresse
misc.php?action=nameoverview

## Templates
- nameoverview 	
- nameoverview_names 	
- nameoverview_overview 	
- nameoverview_overview_divers 	
- nameoverview_surnames

## CSS
**nameoverview.css**
```.name_desc{
	margin: 10px 20px;
	text-align: justify;
}

.name_flexbox{
	display: flex;
	justify-content: space-evenly;
	 flex-flow: row wrap;
	gap: 10px 5px;
}

.name_top_divers{
	width: 24.5%;
	padding: 5px 10px;
	box-sizing: border-box;
	text-align: center;
}

.name_top{
	width: 33%;
	padding: 5px 10px;
	box-sizing: border-box;
	text-align: center;
}

.name_list{
	padding: 5px 10px;
	box-sizing: border-box;
	width: 24.5%;
}

.name_list{
	padding: 5px 10px;
	box-sizing: border-box;
		width: 33%;
}


.name_list > .name{
	margin: 2px auto;
}

.name_list > .name:before{
	content: "» ";
	padding-right: 5px;
}

.name_list > .name span{
	font-weight: bold;	
}

.name_list_divers{
	padding: 5px 10px;
	box-sizing: border-box;
	width: 24.5%;
}

.name_list_divers > .name{
	margin: 2px auto;
}

.name_list_divers > .name:before{
	content: "» ";
	padding-right: 5px;
}

.name_list_divers > .name span{
	font-weight: bold;	
}

.name_alphabet{
	font-weight: bold;
	width: 100%;
}```
