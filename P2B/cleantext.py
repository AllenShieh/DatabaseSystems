#!/usr/bin/env python

"""Clean comment text for easier parsing."""

from __future__ import print_function

import re
import string
import argparse
import json
import re

__author__ = ""
__email__ = ""

# Some useful data.
_CONTRACTIONS = {
    "tis": "'tis",
    "aint": "ain't",
    "amnt": "amn't",
    "arent": "aren't",
    "cant": "can't",
    "couldve": "could've",
    "couldnt": "couldn't",
    "didnt": "didn't",
    "doesnt": "doesn't",
    "dont": "don't",
    "hadnt": "hadn't",
    "hasnt": "hasn't",
    "havent": "haven't",
    "hed": "he'd",
    "hell": "he'll",
    "hes": "he's",
    "howd": "how'd",
    "howll": "how'll",
    "hows": "how's",
    "id": "i'd",
    "ill": "i'll",
    "im": "i'm",
    "ive": "i've",
    "isnt": "isn't",
    "itd": "it'd",
    "itll": "it'll",
    "its": "it's",
    "mightnt": "mightn't",
    "mightve": "might've",
    "mustnt": "mustn't",
    "mustve": "must've",
    "neednt": "needn't",
    "oclock": "o'clock",
    "ol": "'ol",
    "oughtnt": "oughtn't",
    "shant": "shan't",
    "shed": "she'd",
    "shell": "she'll",
    "shes": "she's",
    "shouldve": "should've",
    "shouldnt": "shouldn't",
    "somebodys": "somebody's",
    "someones": "someone's",
    "somethings": "something's",
    "thatll": "that'll",
    "thats": "that's",
    "thatd": "that'd",
    "thered": "there'd",
    "therere": "there're",
    "theres": "there's",
    "theyd": "they'd",
    "theyll": "they'll",
    "theyre": "they're",
    "theyve": "they've",
    "wasnt": "wasn't",
    "wed": "we'd",
    "wedve": "wed've",
    "well": "we'll",
    "were": "we're",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whats": "what's",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "wheres": "where's",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "wholl": "who'll",
    "whore": "who're",
    "whos": "who's",
    "whove": "who've",
    "whyd": "why'd",
    "whyre": "why're",
    "whys": "why's",
    "wont": "won't",
    "wouldve": "would've",
    "wouldnt": "wouldn't",
    "yall": "y'all",
    "youd": "you'd",
    "youll": "you'll",
    "youre": "you're",
    "youve": "you've"
}

# You may need to write regular expressions.
'''
Some clarification about the difference between our outputs and sample outputs:
1. We do not think words on two sides of '/' should be considered as one whole part, they may have completely no relations.
2. We do not think words between "" and '' should be considered as one whole part, especially when content between them is very long.
3. We skip the special character like some smiley face (seems they make no sense in text analysis).
4. Typos are treated as are input intentionally, that is, typos like 'text(s' will be parsed to 'text s'.
5. We have not found a way to treat chinese punctuations, so they are filtered in most cases.
6. There are sometimes one extra space in the end of some parsed texts (does not influence the following processing).
'''

def text2parsed(text):
    parsed_text = ""
    prev = ' '
    i = 0
    # print(text)
    url_exist = False # used for whether [] denotes url, if yes, tell () to skip the content between

    while(i<len(text)):
        if(text[i].lower()=='h'): # deal with http and https
            candidate = ""
            j = i
            while(j<len(text) and text[j]!=' '): # get the potential url
                candidate+=text[j]
                j+=1
            matching = re.match('[hH][tT][tT][pP][sS]?://.*',candidate) # regular expression for http and https
            if(matching):
                # print(text[i+matching.span()[1]])
                i+=matching.span()[1]-1 # start position of undealt character
            else: # if it is just a normal letter 'h'
                parsed_text+=text[i].lower()
                prev = text[i]
        elif(text[i].isalpha() or text[i].isdigit()): # letters
            parsed_text+=text[i].lower()
            prev = text[i]
        elif(text[i] in {'\'', '-', '—', '$', '%'}): # tricky internal, sometimes they just appear alone, e.g. ' - '
            if(i!=0 and i!=len(text)-1 and text[i-1]==' ' and text[i+1]==' '): # deal with lonely internal
                prev = ' '
            else:
                parsed_text+=text[i].lower()
                prev = text[i]
        elif(text[i]=='['): # [xxx](url)
            candidate = ""
            j = i
            while(j<len(text) and text[j-1]!=')'): # get the potential url
                candidate+=text[j]
                j+=1
            # print(candidate)
            matching = re.match('\[.*\]\(.*\)',candidate)
            if(matching):
                # print(text[i+matching.span()[0]])
                # print(text[i+matching.span()[1]])
                url_exist = True # tell () to skip the content between
                # if(prev!=' '):
                #    parsed_text+=' '
                # j = i+1
                # while(text[j]!=']'): # we still need letters inside []
                #    parsed_text+=text[j]
                #    j+=1
                # i+=matching.span()[1]-1 # start position of undealt character
                # prev = text[j-1]
            if(prev!=' '): # if not url, treat it as a common punctuation
                parsed_text+=' '
                prev = ' '
        elif(text[i]=='(' and url_exist): # told by []
            url_exist = False
            while(text[i]!=')'):
                i+=1
        elif(text[i] in {'.', '!', '?', ',', ';', ':'}): # external punctuation (maybe internal)
            if(i!=0 and i!=len(text)-1 and (text[i-1].isalpha() or text[i-1].isdigit()) and (text[i+1].isalpha() or text[i+1].isdigit())):
                parsed_text+=text[i] # if internal
                prev = text[i]
            elif(prev!=' '):
                parsed_text+=(' '+text[i])
                if(i!=len(text)-1): # try not to add extra space, but it doesn't matter
                    parsed_text+=' '
            else:
                parsed_text+=(text[i]+' ')
            prev = ' '
        else: # including \n \t space
            if(prev!=' '):
                parsed_text+=' '
                prev = ' '
        i+=1

    return parsed_text

def parsed2uni(parsed_text):
    unigrams = ""

    parsed_text+=' '
    wordlist = []
    i = 0
    word = ""
    while(i<len(parsed_text)):
        if(parsed_text[i].isdigit() or parsed_text[i].isalpha() or (parsed_text[i] in {'\'', '-', '—', '$', '%'})):
            word+=parsed_text[i]
        elif(parsed_text[i] in {'.', '!', '?', ',', ';', ':'}):
            if((parsed_text[i-1].isalpha() or parsed_text[i-1].isdigit()) and (parsed_text[i+1].isalpha() or parsed_text[i+1].isdigit())):
                word+=parsed_text[i]
        else:
            if(len(word)>0):
                wordlist.append(word)
                word = ""
        i+=1

    for j in range(len(wordlist)):
        if(unigrams!=""):
            unigrams+=' '
        unigrams+=wordlist[j]

    return unigrams

def parsed2bi(parsed_text):
    bigrams = ""

    parsed_text+=' '
    wordlist = []
    i = 0
    word = ""
    while(i<len(parsed_text)):
        if(parsed_text[i].isdigit() or parsed_text[i].isalpha() or (parsed_text[i] in {'\'', '-', '—', '$', '%'})):
            word+=parsed_text[i]
        elif(parsed_text[i] in {'.', '!', '?', ',', ';', ':'}):
            if((parsed_text[i-1].isalpha() or parsed_text[i-1].isdigit()) and (parsed_text[i+1].isalpha() or parsed_text[i+1].isdigit())):
                word+=parsed_text[i]
            elif(len(wordlist)>1):
                for j in range(len(wordlist)-1):
                    if(bigrams!=""):
                        bigrams+=' '
                    bigrams+=(wordlist[j]+'_'+wordlist[j+1])
                wordlist = []
            else:
                wordlist = []
        else:
            if(len(word)>0):
                wordlist.append(word)
                word = ""
        i+=1

    if(len(wordlist)>1):
        for j in range(len(wordlist)-1):
            if(bigrams!=""):
                bigrams+=' '
            bigrams+=(wordlist[j]+'_'+wordlist[j+1])

    return bigrams

def parsed2tri(parsed_text):
    trigrams = ""

    parsed_text+=' '
    wordlist = []
    i = 0
    word = ""
    while(i<len(parsed_text)):
        if(parsed_text[i].isdigit() or parsed_text[i].isalpha() or (parsed_text[i] in {'\'', '-', '—', '$', '%'})):
            word+=parsed_text[i]
        elif(parsed_text[i] in {'.', '!', '?', ',', ';', ':'}):
            if((parsed_text[i-1].isalpha() or parsed_text[i-1].isdigit()) and (parsed_text[i+1].isalpha() or parsed_text[i+1].isdigit())):
                word+=parsed_text[i]
            elif(len(wordlist)>2):
                for j in range(len(wordlist)-2):
                    if(trigrams!=""):
                        trigrams+=' '
                    trigrams+=(wordlist[j]+'_'+wordlist[j+1]+'_'+wordlist[j+2])
                wordlist = []
            else:
                wordlist = []
        else:
            if(len(word)>0):
                wordlist.append(word)
                word = ""
        i+=1

    if(len(wordlist)>2):
        for j in range(len(wordlist)-2):
            if(trigrams!=""):
                trigrams+=' '
            trigrams+=(wordlist[j]+'_'+wordlist[j+1]+'_'+wordlist[j+2])

    return trigrams

def sanitize(text):
    """Do parse the text in variable "text" according to the spec, and return
    a LIST containing FOUR strings
    1. The parsed text.
    2. The unigrams
    3. The bigrams
    4. The trigrams
    """

    # YOUR CODE GOES BELOW:
    # print(text)

    parsed_text = text2parsed(text)
    unigrams = parsed2uni(parsed_text)
    bigrams = parsed2bi(parsed_text)
    trigrams = parsed2tri(parsed_text)

    return [parsed_text, unigrams, bigrams, trigrams]


if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.

    # test = "I'm* *afraid http://x I [can't explainhttps://x myself, sir .Because[x](a)s I [am](h)x [not](s) myself, [you see?"
    test = "As a reminder, this subreddit [is for civil discussion.](/r/politics/wiki/index#wiki_be_civil)\n\nIn general,"
    result = sanitize(test)
    # print(result[0])
    # print(result[1])
    # print(result[2])
    # print(result[3])



    # limit = 10
    out = open("out.txt", "w")
    with open("sample-comments.json") as f:
        for line in f:
            # limit-=1
            data = json.loads(line)
            result = sanitize(data['body'])
            out.write("[\"")
            out.write(result[0])
            out.write('\", \"')
            out.write(result[1])
            out.write('\", \"')
            out.write(result[2])
            out.write('\", \"')
            out.write(result[3])
            out.write('\"]\n')
            # if(limit==0):
            #    break
