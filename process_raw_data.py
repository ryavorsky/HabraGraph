import re
import os
import shutil

source_dir_name = "c:\\Users\\Rostislav\\Documents\\GitHub\\HabraGraph\\hubs_raw_data"
target_dir_name = "c:\\Users\\Rostislav\\Documents\\GitHub\\HabraGraph\\hubs"

# define regular expression to find posts URLs
pattern1 = re.compile(":(\D+)(\d+)/\" class=\"post_title\">([^<]+)<")
pattern2 = re.compile("<meta property=\"og:url\"(\s+)content=\"http:(\D+)(\d+)/")

author_pattern = re.compile("<div class=\"author\">(\s+)<([^<]+)>([^<]+)</a>(\s+)<([^<]+)>([^<]+)</span>")
comment_pattern = re.compile("id=\"comment_(\d+)(\D+)data-parent_id=\"(\d+)\"")
comment_pattern_score = re.compile("score(\D+)(\d+)(\D+)(\d+)(\D+)(\d+)(\D+)(\d+)(\D+)")
comment_pattern_time = re.compile("<time>([^<]+)</time>")
comment_pattern_author = re.compile("class=\"username\">(\S+)</a>")


def process_hub_data(hub_name):
    print "\nProcessing", hub_name
    source_hub_dir = os.path.join(source_dir_name, hub_name)    
    target_hub_dir = os.path.join(target_dir_name, hub_name)    

    if os.path.exists(target_hub_dir):
        print "\nTarget HUB-dir already exists:\n", target_hub_dir
    else :
        os.mkdir(target_hub_dir)
        print "\nTarget HUB-dir created:\n", target_hub_dir

    hub_data_file = open(os.path.join(source_hub_dir, "data.txt"), "r")

    # convert input file into a big string
    inputText = "\n".join(hub_data_file.readlines())
    # find all matches
    ids = pattern1.findall(inputText)             

    # Write the hub summary table into the file
    summary_file = open(os.path.join(target_hub_dir, "summary.txt"), "w")

    for (prefix, post_id, title) in ids :
        url = "http:" + prefix + post_id  
        summary_file.write(post_id + "\t" + url + "\t" + title + "\n")
        
    hub_data_file.close()    
    summary_file.close()
    
    # Process all the posts files for this hub
    source_files = os.listdir(source_hub_dir)

    # First, create subfolder for the files
    make_folder_unless_exists(os.path.join(target_hub_dir, "graphs"))
    make_folder_unless_exists(os.path.join(target_hub_dir, "posts"))

    for file_name in source_files:
        process_post_file(os.path.join(source_hub_dir, file_name), target_hub_dir)


def process_post_file(source_file_name, hub_dir):
    print "Processing", source_file_name
    
    post_file = open(source_file_name, "r")
    inputText = " ".join(post_file.readlines())
    post_file.close()
    
    ids = pattern2.findall(inputText)

    if len(ids) == 1 :
        (space, prefix, post_id) = ids[0]
        url = "http:" + prefix + post_id
        target_file_name = os.path.join(hub_dir, "posts", post_id+".html")
        shutil.copy2(source_file_name, target_file_name)
        extract_graph_from_post(post_id, hub_dir, inputText)
        print post_id, url
    else:
        print "Wrong file"


def extract_graph_from_post(post_id, hub_dir, inputText):
    target_file_name = os.path.join(hub_dir, "graphs", post_id+".data")
    res_file = open(target_file_name, "w")
    res_file.write("\t".join(["post_id", "post_author", "comment_id", "comment_author", "reply_to_id", "time", "score_total", "score_up", "score_down"]) + "\n")
    
    ids = author_pattern.findall(inputText)
    (space1, title, post_author, space2, span, rating)  =  ids[0]
    print post_id, post_author, rating
    
    parts = comment_pattern.split(inputText)
    N = (len(parts) - 1)/4
    for i in range(N):
        comment_id = parts[i*4 + 1]
        reply_to_id  = parts[i*4 + 3]
        rest = parts[i*4 + 4]
        (comment_author, score_total, score_up, score_down, time) = extract_from_comment(rest)
        res_file.write("\t".join([post_id, post_author, comment_id, comment_author, reply_to_id, time, score_total, score_up, score_down]) + "\n")
        
    res_file.close()
        

def extract_from_comment (text):
    scores = comment_pattern_score.findall(text)
    if len(scores) > 0 :
        (t1, score_total, t2, score_up, t3, score_down, t4, score_res, t5) = scores[0]
        score = str([score_total, score_up,score_down])
    else :
        (score_total, score_up, score_down) = ("noscore", "noscore", "noscore")

    times = comment_pattern_time.findall(text)
    if len(times) > 0 :
        time = str(times[0])
    else :
        time = "notime"

    authors = comment_pattern_author.findall(text)
    if len(authors) > 0 :
        author = str(authors[0])
    else :
        author = "noauthor"
        
    return (author, score_total, score_up, score_down, time)


def make_folder_unless_exists (name):
    if os.path.exists(name):
        print "\nDir already exists:\n", name
    else :
        os.mkdir(name)
        print "\nDir created:\n", name


def main():
    print "Source dir name:\n", source_dir_name

    hubs_list = os.listdir(source_dir_name)
    print "\nHubs list:\n", hubs_list

    if os.path.exists(target_dir_name):
        print "\nTarget dir already exists:\n", target_dir_name
    else :
        os.mkdir(target_dir_name)
        print "\nTarget dir created:\n", target_dir_name

    # process_hub_data(hubs_list[0])
    for hub_name in hubs_list:
        process_hub_data(hub_name)


'''
    for hub_name in hubs_list:
        process_hub_data(hub_name)
'''

main()
