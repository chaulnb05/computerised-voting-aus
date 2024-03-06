# Computerising the federal general elections for the House of Representatives

[*Visit the website here*](https://titan.csit.rmit.edu.au/~s3928470/DBA/asg4/index.html)

## Executive Summary
This website is the computerized version of the House of Representatives election in Australia. Voters will go through the same process that they experience when they vote in real life at Voting Centres, from giving their information to filling in the ballots. Oracle Database is used to store all the data required to run the website and for it to function properly. This website was my final project for the Database Applications course at RMIT University. 

## Methodology

### Front-End: the Website
I made the artistic choice to make the website simple yet practical and user-friendly as it is meant to be a governmental website for citizens of all ages and backgrounds. A straight-to-the-point interface will quickly and effortlessly guide users through the voting process without any confusion or distraction. Below are some screenshots of the website:

<img width="412" alt="Screen Shot 2024-03-06 at 11 29 36" src="https://github.com/chaulnb05/computerised-voting-aus/assets/155965902/1ef8f5f3-3325-4195-9ee6-3d10cb4a7abc">
<img width="369" alt="Screen Shot 2024-03-06 at 11 38 03" src="https://github.com/chaulnb05/computerised-voting-aus/assets/155965902/d37eaddf-e42b-4c5f-a466-47ff420c9c0f">


#### Google Maps API
The website implements an [Autocomplete API](https://developers.google.com/maps/documentation/javascript/place-autocomplete) by Google Maps, which fastens the process of filling in information and ensures the consistency and validity of residential information by preventing things like typos made by voters. 
This is also one of the compulsory requirements for this assignment.

<img width="430" alt="Screen Shot 2024-03-05 at 21 31 10" src="https://github.com/chaulnb05/computerised-voting-aus/assets/155965902/e8588597-01a1-4239-b870-48a7b8c8069e"> <img width="409" alt="Screen Shot 2024-03-06 at 11 37 38" src="https://github.com/chaulnb05/computerised-voting-aus/assets/155965902/899c7e0d-73ae-41cc-a462-ff62afdf599f">



### Back-End: the Database
On the other hand, Oracle Database is responsible for the behind-the-scenes work, which in this case is storing all the data about voters and the elections. Every time a voter enters their information, the website will check it against the database to verify whether this voter is registered and whether they have voted. After that, it will automatically match the voter's address to the corresponding Electorate and show the list of candidates in that Electorate.


#### Sample Data
Sample data is provided to store in the database so markers can test the website during the marking process, which can be found in the `sampledata.txt` file. If the website works properly, 2 sample voters will be shown different ballots with different sets of candidates as they are in different Electorates. The list of candidates must also appear correctly in their corresponding Electorate as provided in the sample data. 

It is unlikely that you will be able to test the website now as this has been done by the markers. In other words, sample voters have already been marked in the database as voted. However, this website received full marks so it does function the way it is programmed to.


## Personal Note
This is a project that I'm very passionate about because I put a lot of time and effort into it. It was my first time building a website and after hours of debugging, I was overjoyed to see it up and running properly. I also had a very great time learning PHP and improving my HTML while working on this project. To be honest, I did find this project a highly time-consuming challenge as I had to take on both front-end and back-end roles. Even though the back-end tasks are very relevant to the course, I was not familiar with the front-end side of this project at all, which could be demotivating at first. However, I love learning new things and I also believe that learning more about different parts of a tech project will make me a better team player. 
