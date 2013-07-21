import android, time, ftplib

droid = android.Android()

timestamp = time.strftime('%Y%m%d-%H%M%S', time.localtime())
droid.cameraInteractiveCapturePicture('/sdcard/' + timestamp + '.jpg')

notetext = droid.dialogGetInput('Note', 'Add a note to the photocrumb:').result
note = timestamp + '.txt'
file = open('/sdcard/' + note, 'a')
file.write('%s\n' % (notetext))
file.close()

server = droid.dialogGetInput('FTP Server', 'FTP server IP address:').result
username = droid.dialogGetInput('User name', 'Enter your FTP user name:').result
password = droid.dialogGetPassword('Password', 'Enter your FTP password:').result

droid.dialogCreateAlert('Upload the photocrumb and the note?')
droid.dialogSetPositiveButtonText('Yes')
droid.dialogSetNegativeButtonText('No')
droid.dialogShow()
response = droid.dialogGetResponse().result
	
if response['which'] == 'positive':
	try:
		droid.dialogCreateSpinnerProgress('Uploading to FTP...')
		droid.dialogShow()
		conn = ftplib.FTP(server, username, password)
		file = open ('/sdcard/' + timestamp + '.jpg', 'rb')
		conn.storbinary('STOR  ' + timestamp + '.jpg', file)
		file.close()
		file = open ('/sdcard/' + note, 'rb')
		conn.storbinary('STOR ' + note, file)
		file.close()
		conn.quit()
		droid.notify('Photocrumbs', 'Upload completed.')
		droid.dialogDismiss()
	except Exception:
		droid.dialogCreateAlert('Upload failed.')
		droid.dialogSetPositiveButtonText('OK')
		droid.dialogShow()
