import requests
import random
import os
import shutil
import time

'''
Map tile harvester, with the ability to random-robin requests
among multiple server prefixes. Note that this assumes the
tiles are stored and named using the standard ZXY "quadtiles"
hierarchial storage schema (zoom level n has 4^n tiles; tiles
are stored and retrieved via as Z/X/Y.ext, where X and Y range 
from 0 to 2^n at each zoom level n.
'''

robins = ['a', 'b', 'c', 'd']
tile_root = ".tiles.mapbox.com/v3/elijahmeeks.map-ktkeam22/"

tiles_folder = 'tiles'

overwrite_tiles = False
sleep_interval = .1

min_zoom = 0
max_zoom = 10

# Random-robin tile requests across a set of server prefixes
def rr_tile_request(z,x,y):
  robin = robins[random.randrange(0,4)]
  coords_path = "/".join([z,x,y]) + '.jpg'
  tile_url = 'http://' + robin + tile_root + coords_path
  if os.path.isfile(os.path.join(tiles_folder, coords_path)) and not overwrite_tiles:
    #print("Tile already exists, skipping:",tile_url)
    return None
  print(tile_url)
  time.sleep(sleep_interval)
  try:
    response = requests.get(tile_url, stream=True)
    with open(os.path.join(tiles_folder, coords_path), 'wb') as out_file:
      shutil.copyfileobj(response.raw, out_file)
    del response
  except:
    print("Error requesting tile",tile_url)
    return None
  return None
  
if not os.path.isdir(tiles_folder):
  os.mkdir(tiles_folder)

for z in range(min_zoom, max_zoom):
  if not os.path.isdir(os.path.join(tiles_folder, str(z))):
    os.mkdir(os.path.join(tiles_folder, str(z)))
  for x in range(0, 2**z):
    if not os.path.isdir(os.path.join(tiles_folder, str(z), str(x))):
      os.mkdir(os.path.join(tiles_folder, str(z), str(x)))
    for y in range(0, 2**z):
      rr_tile_request(str(z), str(x), str(y))
