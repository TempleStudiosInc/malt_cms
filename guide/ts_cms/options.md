# TS CMS configuration options

Array item 1 is the node name

'label' : Node name label for admin sidebar (Array)

type : single, or multiple

	Single : single piece of content, index is automatically the edit view
	Multiple : multiple items, index is a listing of all entries

fields : Array

field name : array

label

type

	input
	textarea
	select (pass array of options with key of 'options')
	radio
	checkbox 
	file_audio - Choose from library / Uploader processes asset as audio file
	file_image - Choose from library / Uploader processes asset as image file
	file_raw - Choose from library / Uploader processes asset as raw file
	file_video - Choose from library / Uploader processes asset as video file
	