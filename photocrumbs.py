import android, time, ftplib

droid = android.Android()

timestamp = time.strftime('%Y%m%d-%H%M%S', time.localtime())
droid.cameraInteractiveCapturePicture('/sdcard/' + timestamp + '.jpg')

notetext = droid.dialogGetInput('Note', 'Add a note to the photocrumb:').result
note = timestamp + '.php'
file = open('/sdcard/' + note, 'a')
file.write('%s\n' % (notetext))
file.close()

server = '127.0.0.1'
username = 'username'
password = 'password'


droid.dialogCreateSpinnerProgress('Uploading to FTP...')
droid.dialogShow()
conn = ftplib.FTP(server, username, password)
file = open ('/sdcard/' + timestamp + '.jpg', 'rb')
conn.storbinary('STOR ' + timestamp + '.jpg', file)
file.close()
file = open ('/sdcard/' + note, 'rb')
conn.storbinary('STOR ' + note, file)
file.close()
conn.quit()
droid.notify('Photocrumbs', 'Upload completed.')
droid.dialogDismiss()
