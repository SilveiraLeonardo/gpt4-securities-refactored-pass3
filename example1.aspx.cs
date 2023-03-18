
Imports System.Text.RegularExpressions

Private Sub cmdRunNotePad_Click()
    Dim str As String
    Dim myVar As Integer
    str = window.Text()
    If IsValidInput(str) AndAlso Integer.TryParse(str, myVar) AndAlso myVar > 0 AndAlso myVar <= 10000 Then
        Dim timerDelay As Integer = GetTimerDelay(myVar)
        Dim timer As New System.Threading.Timer(AddressOf OnTimedEvent, Nothing, timerDelay, Timeout.Infinite)
    Else
        MsgBox("Invalid input")
    End If
End Sub

Private Function IsValidInput(input As String) As Boolean
    Dim pattern As String = "^[0-9]+$"
    Dim rgx As New Regex(pattern)
    Return rgx.IsMatch(input)
End Function

Private Function GetTimerDelay(userInput As Integer) As Integer
    ' Define allowed range, for example:
    Dim minValue As Integer = 500
    Dim maxValue As Integer = 5000

    ' Clip the user input value to the allowed range:
    If userInput < minValue Then
        userInput = minValue
    ElseIf userInput > maxValue Then
        userInput = maxValue
    End If

    Return userInput
End Function

Private Sub OnTimedEvent(state As Object)
    ' Do something
End Sub
