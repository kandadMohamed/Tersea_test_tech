<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    .button{
      width: fit-content;
      padding: 10px;
      color: white !important;
      background-color: black;
      text-decoration: none;
    }
    .btn-refuse{
      margin-left: 20px;
      color: black !important;
      background-color: white;
      border: 2px solid black;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div>
    <h4>Hello, {{$mailData['last_name']}} {{$mailData['first_name']}}</h4>
    <h3>{{$mailData['admin_last_name']}} {{$mailData['admin_first_name']}} Invite You to Join {{$mailData['company_name']}} Company</h3>
    <!-- <button>Accepte Invite</button> -->
    <a class='button' href="http://localhost:3000/valide-account/{{$mailData['id']}}">
      Accepte Invite And Valide Profile
    </a>
    <!-- "id": 12,
    "status": "invite",
    "company_nom": "Facebook2",
    "admin_last_name": "Kandad",
    "admin_first_name": "Mohamed" -->
  </div>
</body>
</html>