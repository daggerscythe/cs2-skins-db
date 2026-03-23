import requests
import json
from collections import defaultdict
from datetime import datetime
import time
import random
import sys

steamID = 76561199096796604

url = f"https://steamcommunity.com/inventory/{steamID}/730/2?l=english&count=100"
inventoryData = {}

def getSteamInv():
    try:
        response = requests.get(url)
        response.raise_for_status()
        inventoryData = response.json()

        with open('csgo_inventory.json', 'w') as f:
            json.dump(inventoryData, f, indent=2)

        with open('csgo_inventory.json', 'r') as f:
            inventory = json.load(f)
        print(f"Successfuly loaded {len(inventory.get('assets', []))} items")
        return inventory

    except requests.exceptions.RequestException as e:
        print(f"Error fetching inventory: {e}")
        return None
    
def convertInvToSQL(inventoryData, fetch_prices=False):

    if not inventoryData or 'assets' not in inventoryData:
        print("No inventory data found")
        return [], []

    # ----------------------
    # Mapping tables
    # ----------------------
    # Map Steam weapon TYPE tags -> your Items.ItemID
    item_map = {
        # skins
        'Rifle': 'RIFL',
        'Sniper Rifle': 'SNPR',
        'Pistol': 'PIST',
        'SMG': 'SMG',
        'Machinegun': 'MCHG',
        'Shotgun': 'SHTG',
        'Knife': 'KNIF',
        'Gloves': 'GLOV',
        'Agent': 'AGNT',
        
        # not-skins
        'Sticker': 'OTHR',
        'Music Kit': 'OTHR',
        'Graffiti': 'OTHR',
        'Patch': 'OTHR',
        'Container': 'OTHR',
        'Collectible': 'OTHR',
        'Key': 'OTHR',
        'Pass': 'OTHR'
    }
    
    # What counts as a skin
    skin_item_ids = {'RIFL', 'SNPR', 'PIST', 'SMG', 'SHTG', 'MCHG', 'KNIF', 'GLOV', 'AGNT'}

    # Map exterior/wear text -> FloatID
    float_map = {
        'Factory New': 'FN',
        'Minimal Wear': 'MW',
        'Field-Tested': 'FT',
        'Well-Worn': 'WW',
        'Battle-Scarred': 'BS'
    }

    # Map rarity localized tag -> RarityID
    rarity_map = {
        'Consumer Grade': 'CG',
        'Industrial Grade': 'IG',
        'Mil-Spec Grade': 'MS',
        'Mil-Spec': 'MS',
        'Restricted': 'RS',
        'Classified': 'CL',
        'Covert': 'CV',
        'Extraordinary': 'EX'
    }

   # Map collection names to CollectionIDs
    collection_map = {
        'The Ascent Collection': 'ASC',
        'The Boreal Collection': 'BOREAL', 
        'The Radiant Collection': 'RAD',
        'The Train 2025 Collection': 'TRN25',
        'The Fever Collection': 'FEVER',
        'The Sport & Field Collection': 'SPORT',
        'The Graphic Design Collection': 'GRAPH',
        'Limited Edition Item': 'LIMIT',
        'The Gallery Collection': 'GALL',
        'The Overpass 2024 Collection': 'OVP24',
        'The Kilowatt Collection': 'KILO',
        'The Anubis Collection': 'ANUB',
        'The Revolution Collection': 'REV',
        'The Recoil Collection': 'RECL',
        'The Dreams & Nightmares Collection': 'DNM',
        'The Operation Riptide Collection': 'RIPT',
        'The 2021 Mirage Collection': 'MIR21',
        'The 2021 Vertigo Collection': 'VER21',
        'The 2021 Train Collection': 'TRN21',
        'The 2021 Dust 2 Collection': 'DU221',
        'The Snakebite Collection': 'SNAKE',
        'The Operation Broken Fang Collection': 'OBF',
        'The Ancient Collection': 'ANC',
        'The Control Collection': 'CTRL',
        'The Havoc Collection': 'HAVOC',
        'The Fracture Collection': 'FRAC',
        'The Prisma 2 Collection': 'PRIS2',
        'The Canals Collection': 'CANAL',
        'The Norse Collection': 'NORSE',
        'The St. Marc Collection': 'STMAR',
        'The Shattered Web Collection': 'SWEB',
        'The CS20 Collection': 'CS20',
        'The X-Ray Collection': 'XRAY',
        'The Prisma Collection': 'PRIS',
        'The Clutch Collection': 'CLTCH',
        'The Danger Zone Collection': 'DZ',
        'The 2018 Inferno Collection': 'INF18',
        'The 2018 Nuke Collection': 'NUK18',
        'The Horizon Collection': 'HZN',
        'The Spectrum 2 Collection': 'SPEC2',
        'The Operation Hydra Collection': 'HYDRA',
        'The Spectrum Collection': 'SPEC',
        'The Glove Collection': 'GLOVE',
        'The Gamma 2 Collection': 'GAM2',
        'The Gamma Collection': 'GAM',
        'The Chroma 3 Collection': 'CHR3',
        'The Chroma 2 Collection': 'CHR2',
        'The Chroma Collection': 'CHR',
        'The Wildfire Collection': 'WF',
        'The Revolver Case Collection': 'REVLV',
        'The Shadow Collection': 'SHAD',
        'The Falchion Collection': 'FALC',
        'The Vanguard Collection': 'VAN',
        'The Cache Collection': 'CACHE',
        'The eSports 2014 Summer Collection': 'ES14S',
        'The Cobblestone Collection': 'COBB',
        'The Breakout Collection': 'BRK',
        'The Overpass Collection': 'OVP',
        'The Huntsman Collection': 'HUNT',
        'The Phoenix Collection': 'PHX',
        'The Arms Deal 3 Collection': 'AD3',
        'The Winter Offensive Collection': 'WO',
        'The eSports 2013 Winter Collection': 'ES13W',
        'The Mirage Collection': 'MIR',
        'The Dust 2 Collection': 'DU2',
        'The Train Collection': 'TRN',
        'The Inferno Collection': 'INF',
        'The Arms Deal 2 Collection': 'AD2',
        'The Bravo Collection': 'BRAVO',
        'The eSports 2013 Collection': 'ES13',
        'The Arms Deal Collection': 'AD1',
        'The Vertigo Collection': 'VER',
        'The Nuke Collection': 'NUKE',
        'The Italy Collection': 'ITLY',
        'The Lake Collection': 'LAKE', 
        'The Bank Collection': 'BANK',
        'The Safehouse Collection': 'SFHS'
    }
    
    # Build description map (classid_instanceid -> description)
    descriptions_map = {}
    for desc in inventoryData.get('descriptions', []):
        key = f"{desc.get('classid','')}_{desc.get('instanceid','')}"
        descriptions_map[key] = desc

    # Build asset properties map (assetid -> properties)
    asset_properties_map = {}
    for prop_entry in inventoryData.get('asset_properties', []):  # <-- FIXED!
        assetid = prop_entry.get('assetid')
        properties = prop_entry.get('asset_properties', [])
        asset_properties_map[assetid] = properties

    # Helper for sanitizing SQL strings
    def sql_escape(s):
        """Escape single quotes for SQL string literal."""
        if s is None:
            return ''
        s = s.replace("'", "''")
        s = s.replace('StatTrak™', 'StatTrak')
        return s
    # Pattern extraction from asset properties
    def extract_pattern(asset_data, description):
        assetid = asset_data.get('assetid')
        
        # Get properties from our properties map
        properties = asset_properties_map.get(assetid, [])
        
        # Look for Pattern Template in asset properties
        for prop in properties:
            if prop.get('name') == 'Pattern Template':
                try:
                    pattern_val = prop.get('int_value')
                    if pattern_val is not None:
                        return int(pattern_val)
                except (ValueError, TypeError) as e:
                    print(f"Pattern conversion error: {e}")
                    continue
        return 0



    # SkinID generation mappings
    item_type_to_num = {
        'RIFL': 1, 'SNPR': 2, 'PIST': 3, 'SMG': 4, 'SHTG': 5, 'MCHG': 6, 'KNIF': 7, 'GLOV': 7, 'AGNT': 7, 'OTHR': 8
    }
    float_to_num = {'FN':1,'MW':2,'FT':3,'WW':4,'BS':5}
    rarity_to_num = {'CG':1,'IG':2,'MS':3,'RS':4,'CL':5,'CV':6,'EX':7}

    used_skinids = set()
    collision_counters = defaultdict(int)

    def make_skin_id(classid_str, itemid, floatid, rarityid):
        # part1: item type
        p1 = item_type_to_num.get(itemid, 8)
        # part2: float
        p2 = float_to_num.get(floatid, 1)
        # part3: rarity
        p3 = rarity_to_num.get(rarityid, 1)

        # part4: first 4 digits from classid
        digits = ''.join(ch for ch in str(classid_str) if ch.isdigit())
        if len(digits) >= 4:
            p4 = digits[:4]
        else:
            p4 = (digits + '0000')[:4]

        base = f"{p1}{p2}{p3}{p4}"
        # Convert to int and handle collisions
        try:
            base_int = int(base)
        except:
            base_int = abs(hash(base)) % 10**10

        if base_int in used_skinids:
            collision_counters[base_int] += 1
            final_id = int(f"{base_int}{collision_counters[base_int]}")
        else:
            final_id = base_int

        used_skinids.add(final_id)
        return final_id

    def get_collection_id(description):
        """Extract collection ID from item tags"""
        tags = description.get('tags', [])
        for tag in tags:
            if tag.get('category') == 'ItemSet':
                collection_name = tag.get('localized_tag_name', '')
                return collection_map.get(collection_name)
        return None

    # Main loop: build INSERT statements
    skin_commands = []
    collection_commands = []
    generated_count = 0
    skin_data_for_prices = []

    for asset in inventoryData['assets']:
        key = f"{asset.get('classid','')}_{asset.get('instanceid','')}"
        desc = descriptions_map.get(key, {})

        market_hash_name = desc.get('market_hash_name') or desc.get('name') or 'Unknown Skin'
        
        # Remove wear suffixes from display name
        skin_name = market_hash_name
        for wear in ('(Factory New)', '(Minimal Wear)', '(Field-Tested)', '(Well-Worn)', '(Battle-Scarred)'):
            skin_name = skin_name.replace(wear, '').strip()
        skin_name = sql_escape(skin_name)

        # Parse tags
        tags = desc.get('tags', []) or []
        tag_dict = {t.get('category'): t for t in tags}

        # Get item type from "Type" tag
        type_local = tag_dict.get('Type', {}).get('localized_tag_name', '')
        item_id = item_map.get(type_local, 'OTHR')

        # Skip non-skin items
        if item_id not in skin_item_ids:
            continue

        # Extract pattern, rarity, and exterior
        pattern_id = extract_pattern(asset, desc)

        rarity_local = tag_dict.get('Rarity', {}).get('localized_tag_name')
        if not rarity_local:
            rarity_local = desc.get('rarity') or desc.get('type') or 'Consumer Grade'
        rarity_code = rarity_map.get(rarity_local, 'CG')

        exterior_local = tag_dict.get('Exterior', {}).get('localized_tag_name') or tag_dict.get('Quality', {}).get('localized_tag_name')
        if not exterior_local:
            for t in tags:
                if t.get('category', '').lower() in ('exterior', 'wear', 'quality'):
                    exterior_local = t.get('localized_tag_name')
                    break
        float_code = float_map.get(exterior_local, 'FN')

        # Build skin ID
        classid = asset.get('classid', '')
        skin_id = make_skin_id(classid, item_id, float_code, rarity_code)

        # Check for StatTrak
        isStatTrak = 1 if "StatTrak" in market_hash_name else 0

        # Build SQL insert for Skins
        skin_sql = (
            "INSERT INTO Skins (SkinID, Skin_Name, ItemID, Pattern, FloatID, RarityID, StatTrak) VALUES "
            f"({skin_id}, '{skin_name}', '{item_id}', {pattern_id}, '{float_code}', '{rarity_code}', {isStatTrak});"
        )
        skin_commands.append(skin_sql)

        skin_data_for_prices.append((skin_id, market_hash_name))

        # Build collection relationship if exists
        collection_id = get_collection_id(desc)
        if collection_id:
            collection_sql = (
                "INSERT INTO SkinInCollection (CollectionID, SkinID) VALUES "
                f"('{collection_id}', {skin_id});"
            )
            collection_commands.append(collection_sql)

        generated_count += 1

    market_commands = []
    if fetch_prices:
        skin_prices = get_current_prices(skin_data_for_prices)
        market_commands = generate_marketvalue_inserts(skin_prices)
    else:
        print("Skipping price fetching as requested")

    print(f"Generated {generated_count} Skins INSERT statements and {len(collection_commands)} SkinInCollection INSERT statements.")
    return skin_commands, collection_commands, market_commands

def get_steam_market_price(market_hash_name):
    """Get current price from Steam Community Market"""
    url = f"https://steamcommunity.com/market/priceoverview/"
    params = {
        'appid': 730,
        'currency': 1,  # USD
        'market_hash_name': market_hash_name
    }

    try:
        response = requests.get(url, params=params, timeout=10)
        response.raise_for_status()
        data = response.json()

        if data.get('success'):
            price_str = data.get('lowest_price', '$0.00')
            price_clean = price_str.replace('$', '').replace(',', '')
            return float(price_clean)
        else:
            print(f"Steam API error for {market_hash_name}")
            return 0.00
            
    except requests.exceptions.RequestException as e:
        print(f"Request failed for {market_hash_name}: {e}")
        return 0.00
    except ValueError as e:
        print(f"Price conversion failed for {market_hash_name}: {e}")
        return 0.00

def get_current_prices(skin_data_list):
    """
    skin_data_list: List of tuples (SkinID, market_hash_name)
    Returns: Dict {SkinID: current_price}
    """
    prices = {}
    total_skins = len(skin_data_list)
    
    print(f"Fetching prices for {total_skins} skins from Steam Market...")
    
    for i, (skin_id, market_hash_name) in enumerate(skin_data_list, 1):
        print(f"({i}/{total_skins}) {market_hash_name}")
        
        price = get_steam_market_price(market_hash_name)
        print(f"  Price: ${price:.2f}")
        prices[skin_id] = price
        
        if i < total_skins:
            sleep_time = random.uniform(5, 8)
            print(f"  Sleeping for {sleep_time:.1f} seconds...")
            time.sleep(sleep_time)
    
    print("Price fetching complete!")
    return prices

def generate_marketvalue_inserts(skin_prices):
    """Generate MarketValue INSERT statements"""
    commands = []
    current_date = datetime.now().strftime('%Y-%m-%d')
    
    for skin_id, price in skin_prices.items():
        sql = f"INSERT INTO MarketValue (SkinID, Price, Date) VALUES ({skin_id}, {price:.2f}, '{current_date}');"
        commands.append(sql)
    
    return commands

def convertInvToSQL_collections_only(inventoryData):
    """
    Only generate SkinInCollection INSERT statements without price fetching.
    Reuses all the existing logic but skips market data.
    """
    # Call the full function but ignore market commands
    skin_commands, collection_commands, _ = convertInvToSQL(inventoryData)
    return skin_commands, collection_commands


def main():
    while True:
        print("\n=== Steam Inventory SQL Converter ===")
        print("1. Fetch new inventory from Steam")
        print("2. Generate Skin table inserts")
        print("3. Generate SkinInCollection bridge table inserts") 
        print("4. Generate MarketValue table inserts")
        print("-1. Exit")
        choice = input("Choose an option: ").strip()

        if choice == '-1':
            print("Goodbye!")
            break
            
        elif choice == '1':
            inventory = getSteamInv()
            if inventory:
                print("Inventory data saved to csgo_inventory.json")
            continue
            
        elif choice in ['2', '3', '4']:
            try:
                with open('csgo_inventory.json', 'r') as f:
                    inventory = json.load(f)
                    print(f"Loaded {len(inventory.get('assets', []))} items from file.")
            except FileNotFoundError:
                print("csgo_inventory.json not found. Please fetch inventory first (Option 1).")
                continue

            if not inventory:
                print("No inventory data found.")
                continue

            if choice == '2':
                skin_commands, _, _ = convertInvToSQL(inventory, fetch_prices=False)
                with open('skin_inserts.sql', 'w') as f:
                    f.write("-- Skins table inserts\n")
                    for cmd in skin_commands:
                        f.write(cmd + '\n')
                print(f"Generated {len(skin_commands)} Skin INSERT statements")

            elif choice == '3':
                _, collection_commands, _ = convertInvToSQL(inventory, fetch_prices=False)
                with open('skin_collection_inserts.sql', 'w') as f:
                    f.write("-- SkinInCollection bridge table inserts\n")
                    for cmd in collection_commands:
                        f.write(cmd + '\n')
                print(f"Generated {len(collection_commands)} SkinInCollection INSERT statements")

            elif choice == '4':
                print("Starting price fetch...")
                _, _, market_commands = convertInvToSQL(inventory, fetch_prices=True)
                
                with open('market_value_inserts.sql', 'w') as f:
                    f.write("-- MarketValue table inserts\n")
                    for cmd in market_commands:
                        f.write(cmd + '\n')
                print(f"Generated {len(market_commands)} MarketValue INSERT statements")

        else:
            print("Invalid choice. Please try again.")
       
if __name__ == "__main__":
    main()