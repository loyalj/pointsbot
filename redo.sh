#!/bin/bash
docker stop pointsbot
docker rm pointsbot
docker build -t loyalj/pointsbot .
docker run -d -p 80:80 --name pointsbot --link mongodb:mongodb loyalj/pointsbot
