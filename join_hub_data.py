import re
import os
import shutil

dir_name = "c:\\Viz\\hubs"

def main():
    print "Source dir name:\n", dir_name
    
    hubs_list = os.listdir(dir_name)
    print "\nHubs list:\n", hubs_list

    for hub_name in hubs_list:
        if os.path.isdir(os.path.join(dir_name, hub_name)):
            join_data_files(hub_name)


# Join all the posts files for this hub
def join_data_files(hub_name):
    
    source_dir = os.path.join(dir_name, hub_name, "graphs")
    source_files = os.listdir(source_dir)
    
    target_file_name = os.path.join(dir_name, hub_name + ".data")

    res = []
    
    print target_file_name
    print "Processing", len(source_files), "data files in the hub..."

    for file_name in source_files :
        data_file = open(os.path.join(source_dir, file_name), "r")
        data = data_file.readlines()
        head = data.pop(0)

        data = extend_data(data)
        
        res = res + data
        data_file.close()

    res_file = open(target_file_name, "w")
    res_file.write(head.rstrip() + "\tparent_author\tdepth\tchildren_number\n" )
    res_file.write("".join(res))
    res_file.close()


def extend_data(data) :

    if len(data) == 0 :
        return data

    d_table = [dataline.rstrip().split("\t") for dataline in data]
    ids = [d_line[2] for d_line in d_table] + ['0']
    authors = [d_line[3] for d_line in d_table] + [d_table[0][1]]
    parent = [d_line[4] for d_line in d_table]
    
    # extend with author of comment_on_id
    d_table = [d_line + [str(authors[ids.index(d_line[4])])] for d_line in d_table]

    # extend with depth
    depth = [0 for i in ids ]
    n = len(d_table)
    for i in range(n) :
        if parent[i] == '0' :
            depth[i] = 1
        else:
            parent_index = ids.index(parent[i])
            depth[i] = depth[parent_index] + 1
    d_table = [ d_table[i] + [str(depth[i])] for i in range(n) ]

    # extend with number of comments on this comment
    d_table = [d_line+[str(parent.count(d_line[2]))] for d_line in d_table]

    res = ["\t".join(d_line) + "\n" for d_line in d_table]
    return res

main()
