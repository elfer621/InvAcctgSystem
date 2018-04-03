;FileOpen(1, AppDomain.CurrentDomain.BaseDirectory & "open.txt", OpenMode.Output)
;   PrintLine(1, Chr(27) & Chr(112) & Chr(0) & Chr(25) & Chr(250))
;   FileClose(1)

;   Shell("print /d:com1 open.txt", AppWinStyle.Hide)


;Local Const $sOpenCashDrawer = 27,112,48,25,250
;Local Const $sOpenCashDrawer = Chr(27) & 'p0' & Chr(25) & ChrW(250)
;FileWrite("test.txt", $sOpenCashDrawer)
;If FileCopy("test.txt", "\\POS-PC\RECEIPT", 1) Then ConsoleWrite("ESC-P string sent" & @LF)

;MsgBox(0, "AutoIt Example", "This is line 1" & @CRLF & "This is line 2" & @CRLF & "This is line 3")

