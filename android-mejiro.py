import android, time, os, ftplib
# Specify FTP connection info
server = '127.0.0.1'
username = 'username'
password = 'password'
droid = android.Android()
if not os.path.exists('/sdcard/mejiro/'):
    os.makedirs('/sdcard/mejiro/')
timestamp = time.strftime('%Y%m%d-%H%M%S', time.localtime())
droid.cameraInteractiveCapturePicture('/sdcard/mejiro/' + timestamp + '.jpg')
description_text = droid.dialogGetInput('Description', 'Add a description to the photo:').result
description_file = timestamp + '.txt'
file = open('/sdcard/mejiro/' + description_file, 'a')
file.write('%s\n' % (description_text))
file.close()
droid.dialogCreateAlert('Upload the photo and the description file?')
droid.dialogSetPositiveButtonText('Yes')
droid.dialogSetNegativeButtonText('No')
droid.dialogShow()
response = droid.dialogGetResponse().result
if response['which'] == 'positive':
    droid.dialogCreateSpinnerProgress('Uploading to FTP...')
    droid.dialogShow()
    conn = ftplib.FTP(server, username, password)
    file = open ('/sdcard/mejiro/' + timestamp + '.jpg', 'rb')
    conn.storbinary('STOR ' + timestamp + '.jpg', file)
    file.close()
    file = open ('/sdcard/mejiro/' + description_file, 'rb')
    conn.storbinary('STOR ' + description_file, file)
    file.close()
    conn.quit()
    droid.notify('Mejiro', 'Upload completed.')
    droid.dialogDismiss()
else:
    droid.notify('Mejiro', 'All done!')
    droid.dialogDismiss() 
